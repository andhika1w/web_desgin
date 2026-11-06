<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * CHECK constraint, VIEW, TRIGGER & STORED PROCEDURE.
 *
 * Fitur ini spesifik MySQL 8.0.16+ (CHECK constraint, SIGNAL, ST_Distance_Sphere),
 * jadi hanya dijalankan jika koneksi memakai driver mysql/mariadb.
 *
 * Sebelum eksekusi per-request, backend dapat mengisi:
 *   DB::statement('SET @app_user_id = ?', [auth()->id()]);
 * agar log status mencatat siapa yang mengubah.
 */
return new class extends Migration
{
    private function isMySql(): bool
    {
        return in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true);
    }

    private function addCheckConstraint(string $table, string $name, string $check): void
    {
        $exists = DB::selectOne('
            SELECT 1 FROM information_schema.TABLE_CONSTRAINTS
             WHERE CONSTRAINT_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND CONSTRAINT_NAME = ?
        ', [$table, $name]);

        if (! $exists) {
            DB::statement("ALTER TABLE `{$table}` ADD CONSTRAINT `{$name}` CHECK ({$check})");
        }
    }

    public function up(): void
    {
        if (! $this->isMySql()) {
            return;
        }

        // -----------------------------------------------------------------
        // CHECK CONSTRAINT (idempotent, cek keberadaan dulu)
        // -----------------------------------------------------------------
        $this->addCheckConstraint('tps', 'chk_tps_kapasitas', 'kapasitas_total_kg > 0 AND kapasitas_terpakai_kg >= 0');
        $this->addCheckConstraint('tps_jenis_sampah', 'chk_tjs', 'kapasitas_maks_kg >= 0 AND terpakai_kg >= 0');
        $this->addCheckConstraint('setoran_detail', 'chk_sd_berat', 'berat_estimasi_kg > 0 AND (berat_aktual_kg IS NULL OR berat_aktual_kg >= 0)');
        $this->addCheckConstraint('ulasan', 'chk_rating', '(rating_kurir IS NULL OR rating_kurir BETWEEN 1 AND 5) AND (rating_tps IS NULL OR rating_tps BETWEEN 1 AND 5)');
        $this->addCheckConstraint('transaksi_poin', 'chk_poin', 'jumlah > 0');

        // -----------------------------------------------------------------
        // VIEW (CREATE OR REPLACE aman jika sudah ada)
        // -----------------------------------------------------------------
        DB::statement(<<<'SQL'
            CREATE OR REPLACE VIEW v_kapasitas_tps AS
            SELECT t.tps_id, t.nama_tps, t.status,
                   t.kapasitas_total_kg, t.kapasitas_terpakai_kg,
                   (t.kapasitas_total_kg - t.kapasitas_terpakai_kg) AS sisa_kg,
                   ROUND(t.kapasitas_terpakai_kg / t.kapasitas_total_kg * 100, 1) AS persen_terisi
            FROM tps t
        SQL);

        DB::statement(<<<'SQL'
            CREATE OR REPLACE VIEW v_setoran_masuk_tps AS
            SELECT s.setoran_id, s.kode_setoran, s.tps_id, s.metode, s.status, s.estimasi_tiba,
                   u.nama  AS nama_pengguna,
                   kr.nama AS nama_kurir,
                   SUM(d.berat_estimasi_kg)            AS total_estimasi_kg,
                   GROUP_CONCAT(j.nama SEPARATOR ', ') AS jenis_sampah
            FROM setoran s
            JOIN users u            ON u.user_id = s.pengguna_id
            JOIN setoran_detail d   ON d.setoran_id = s.setoran_id
            JOIN jenis_sampah j     ON j.jenis_id = d.jenis_id
            LEFT JOIN penjemputan p ON p.setoran_id = s.setoran_id
            LEFT JOIN users kr      ON kr.user_id = p.kurir_id
            WHERE s.status IN ('diangkut','menuju_tps','tiba_di_tps')
            GROUP BY s.setoran_id, s.kode_setoran, s.tps_id, s.metode, s.status, s.estimasi_tiba, u.nama, kr.nama
        SQL);

        DB::statement(<<<'SQL'
            CREATE OR REPLACE VIEW v_tugas_angkut AS
            SELECT p.penjemputan_id, p.kurir_id, p.status AS status_penjemputan,
                   s.setoran_id, s.kode_setoran, p.jadwal_jemput,
                   a.alamat_lengkap AS alamat_jemput, a.latitude AS jemput_lat, a.longitude AS jemput_lng,
                   t.nama_tps, t.alamat AS alamat_tps, t.latitude AS tps_lat, t.longitude AS tps_lng
            FROM penjemputan p
            JOIN setoran s         ON s.setoran_id = p.setoran_id
            JOIN alamat_pengguna a ON a.alamat_id = p.alamat_id
            JOIN tps t             ON t.tps_id = s.tps_id
        SQL);

        // -----------------------------------------------------------------
        // TRIGGER (DROP IF EXISTS dulu agar tidak bentrok)
        // -----------------------------------------------------------------
        DB::unprepared('DROP TRIGGER IF EXISTS trg_setoran_bi');
        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_setoran_bi BEFORE INSERT ON setoran FOR EACH ROW
            BEGIN
              IF NEW.metode = 'dijemput' THEN
                SET NEW.status = 'menunggu_kurir';
              ELSE
                SET NEW.status = 'menuju_tps';
              END IF;
            END
        SQL);

        DB::unprepared('DROP TRIGGER IF EXISTS trg_setoran_detail_bi');
        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_setoran_detail_bi BEFORE INSERT ON setoran_detail FOR EACH ROW
            BEGIN
              DECLARE v_tps       BIGINT UNSIGNED;
              DECLARE v_status    VARCHAR(10);
              DECLARE v_izin      TINYINT(1);
              DECLARE v_kategori  VARCHAR(20);
              DECLARE v_diterima  TINYINT(1);
              DECLARE v_sisa      DECIMAL(12,2);

              SELECT t.tps_id, t.status, t.izin_limbah_medis
                INTO v_tps, v_status, v_izin
                FROM setoran s JOIN tps t ON t.tps_id = s.tps_id
               WHERE s.setoran_id = NEW.setoran_id;

              SELECT kategori INTO v_kategori FROM jenis_sampah WHERE jenis_id = NEW.jenis_id;

              SELECT diterima, (kapasitas_maks_kg - terpakai_kg)
                INTO v_diterima, v_sisa
                FROM tps_jenis_sampah
               WHERE tps_id = v_tps AND jenis_id = NEW.jenis_id;

              IF v_status <> 'buka' THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'TPS sedang tutup atau penuh';
              END IF;
              IF v_diterima IS NULL OR v_diterima = 0 THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'TPS tidak menerima jenis sampah ini';
              END IF;
              IF v_kategori = 'medis' AND v_izin = 0 THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'TPS tidak memiliki izin limbah medis';
              END IF;
              IF v_sisa < NEW.berat_estimasi_kg THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Kapasitas TPS untuk jenis ini tidak mencukupi';
              END IF;
            END
        SQL);

        DB::unprepared('DROP TRIGGER IF EXISTS trg_setoran_ai');
        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_setoran_ai AFTER INSERT ON setoran FOR EACH ROW
            BEGIN
              INSERT INTO setoran_status_log (setoran_id, status_lama, status_baru, diubah_oleh)
              VALUES (NEW.setoran_id, NULL, NEW.status, NEW.pengguna_id);

              IF NEW.metode = 'antar_sendiri' THEN
                INSERT INTO notifikasi (penerima_id, setoran_id, tipe, judul, pesan)
                SELECT t.pengelola_id, NEW.setoran_id, 'sampah_akan_diantar',
                       'Sampah akan diantar pengguna',
                       CONCAT('Setoran ', NEW.kode_setoran, ' akan diantar sendiri oleh pengguna ke ', t.nama_tps)
                  FROM tps t WHERE t.tps_id = NEW.tps_id;
              ELSE
                INSERT INTO notifikasi (penerima_id, setoran_id, tipe, judul, pesan)
                SELECT k.user_id, NEW.setoran_id, 'tugas_baru',
                       'Tugas angkut baru',
                       CONCAT('Setoran ', NEW.kode_setoran, ' menunggu diangkut. TPS tujuan: ', t.nama_tps, ', ', t.alamat)
                  FROM kurir_profil k JOIN tps t ON t.tps_id = NEW.tps_id
                 WHERE k.status_ketersediaan = 'online';
              END IF;
            END
        SQL);

        DB::unprepared('DROP TRIGGER IF EXISTS trg_setoran_au');
        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_setoran_au AFTER UPDATE ON setoran FOR EACH ROW
            BEGIN
              IF NOT (OLD.status <=> NEW.status) THEN
                INSERT INTO setoran_status_log (setoran_id, status_lama, status_baru, diubah_oleh)
                VALUES (NEW.setoran_id, OLD.status, NEW.status, @app_user_id);
              END IF;
            END
        SQL);

        DB::unprepared('DROP TRIGGER IF EXISTS trg_penjemputan_au');
        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_penjemputan_au AFTER UPDATE ON penjemputan FOR EACH ROW
            BEGIN
              IF NOT (OLD.status <=> NEW.status) THEN

                IF NEW.status = 'diterima' THEN
                  UPDATE setoran SET status = 'kurir_ditugaskan' WHERE setoran_id = NEW.setoran_id;
                  UPDATE kurir_profil SET status_ketersediaan = 'bertugas' WHERE user_id = NEW.kurir_id;
                  INSERT INTO notifikasi (penerima_id, setoran_id, tipe, judul, pesan)
                  SELECT s.pengguna_id, s.setoran_id, 'kurir_ditugaskan',
                         'Kurir menuju lokasi Anda',
                         CONCAT('Kurir telah menerima penjemputan setoran ', s.kode_setoran)
                    FROM setoran s WHERE s.setoran_id = NEW.setoran_id;

                ELSEIF NEW.status = 'diangkut' THEN
                  UPDATE setoran SET status = 'diangkut' WHERE setoran_id = NEW.setoran_id;
                  INSERT INTO notifikasi (penerima_id, setoran_id, tipe, judul, pesan)
                  SELECT t.pengelola_id, s.setoran_id, 'sampah_akan_diantar',
                         'Sampah akan diantar kurir',
                         CONCAT('Kurir membawa setoran ', s.kode_setoran, ' menuju ', t.nama_tps)
                    FROM setoran s JOIN tps t ON t.tps_id = s.tps_id
                   WHERE s.setoran_id = NEW.setoran_id;

                ELSEIF NEW.status = 'tiba_di_tps' THEN
                  UPDATE setoran SET status = 'tiba_di_tps' WHERE setoran_id = NEW.setoran_id;
                  UPDATE kurir_profil SET status_ketersediaan = 'online' WHERE user_id = NEW.kurir_id;
                  INSERT INTO notifikasi (penerima_id, setoran_id, tipe, judul, pesan)
                  SELECT t.pengelola_id, s.setoran_id, 'sampah_tiba',
                         'Sampah tiba di TPS',
                         CONCAT('Setoran ', s.kode_setoran, ' sudah tiba dan siap ditimbang')
                    FROM setoran s JOIN tps t ON t.tps_id = s.tps_id
                   WHERE s.setoran_id = NEW.setoran_id;

                ELSEIF NEW.status = 'dibatalkan' THEN
                  IF NEW.kurir_id IS NOT NULL THEN
                    UPDATE kurir_profil SET status_ketersediaan = 'online' WHERE user_id = NEW.kurir_id;
                  END IF;
                END IF;

              END IF;
            END
        SQL);

        DB::unprepared('DROP TRIGGER IF EXISTS trg_tjs_au');
        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_tjs_au AFTER UPDATE ON tps_jenis_sampah FOR EACH ROW
            BEGIN
              DECLARE v_total DECIMAL(12,2);

              SELECT COALESCE(SUM(terpakai_kg), 0) INTO v_total
                FROM tps_jenis_sampah WHERE tps_id = NEW.tps_id;

              UPDATE tps
                 SET kapasitas_terpakai_kg = v_total,
                     status = CASE
                                WHEN status = 'buka'  AND v_total >= kapasitas_total_kg THEN 'penuh'
                                WHEN status = 'penuh' AND v_total <  kapasitas_total_kg THEN 'buka'
                                ELSE status
                              END
               WHERE tps_id = NEW.tps_id;
            END
        SQL);

        // -----------------------------------------------------------------
        // STORED PROCEDURE (DROP IF EXISTS dulu agar tidak bentrok)
        // -----------------------------------------------------------------
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_selesaikan_setoran');
        DB::unprepared(<<<'SQL'
            CREATE PROCEDURE sp_selesaikan_setoran(IN p_setoran_id BIGINT UNSIGNED)
            BEGIN
              DECLARE v_tps     BIGINT UNSIGNED;
              DECLARE v_user    BIGINT UNSIGNED;
              DECLARE v_status  VARCHAR(20);
              DECLARE v_kode    VARCHAR(25);
              DECLARE v_berat   DECIMAL(10,2);
              DECLARE v_poin    INT;

              DECLARE EXIT HANDLER FOR SQLEXCEPTION
              BEGIN
                ROLLBACK;
                RESIGNAL;
              END;

              START TRANSACTION;

              SELECT tps_id, pengguna_id, status, kode_setoran
                INTO v_tps, v_user, v_status, v_kode
                FROM setoran WHERE setoran_id = p_setoran_id FOR UPDATE;

              IF v_status IS NULL OR v_status <> 'tiba_di_tps' THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Setoran tidak ditemukan atau belum tiba di TPS';
              END IF;

              IF EXISTS (SELECT 1 FROM setoran_detail
                          WHERE setoran_id = p_setoran_id AND berat_aktual_kg IS NULL) THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Berat aktual belum diisi untuk semua jenis sampah';
              END IF;

              SELECT COALESCE(SUM(d.berat_aktual_kg), 0),
                     COALESCE(FLOOR(SUM(d.berat_aktual_kg * j.poin_per_kg)), 0)
                INTO v_berat, v_poin
                FROM setoran_detail d JOIN jenis_sampah j ON j.jenis_id = d.jenis_id
               WHERE d.setoran_id = p_setoran_id;

              -- Tambah kapasitas terpakai per jenis (trigger trg_tjs_au menyinkronkan total TPS)
              UPDATE tps_jenis_sampah tjs
                JOIN setoran_detail d ON d.jenis_id = tjs.jenis_id AND d.setoran_id = p_setoran_id
                 SET tjs.terpakai_kg = tjs.terpakai_kg + d.berat_aktual_kg
               WHERE tjs.tps_id = v_tps;

              UPDATE setoran
                 SET status = 'selesai', total_berat_aktual_kg = v_berat,
                     total_poin = v_poin, selesai_at = NOW()
               WHERE setoran_id = p_setoran_id;

              IF v_poin > 0 THEN
                INSERT INTO transaksi_poin (pengguna_id, setoran_id, tipe, jumlah, keterangan)
                VALUES (v_user, p_setoran_id, 'masuk', v_poin, CONCAT('Poin setoran ', v_kode));
              END IF;

              INSERT INTO notifikasi (penerima_id, setoran_id, tipe, judul, pesan)
              VALUES (v_user, p_setoran_id, 'setoran_selesai', 'Setoran selesai',
                      CONCAT('Setoran ', v_kode, ' diterima seberat ', v_berat, ' kg. Poin: ', v_poin));

              COMMIT;
            END
        SQL);

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_rekomendasi_tps');
        DB::unprepared(<<<'SQL'
            CREATE PROCEDURE sp_rekomendasi_tps(
              IN p_lat      DECIMAL(10,7),
              IN p_lng      DECIMAL(10,7),
              IN p_jenis_id BIGINT UNSIGNED,
              IN p_berat    DECIMAL(10,2))
            BEGIN
              SELECT t.tps_id, t.nama_tps, t.alamat,
                     ROUND(ST_Distance_Sphere(POINT(p_lng, p_lat), POINT(t.longitude, t.latitude)) / 1000, 2) AS jarak_km,
                     (tj.kapasitas_maks_kg - tj.terpakai_kg) AS sisa_kg
                FROM tps t
                JOIN tps_jenis_sampah tj ON tj.tps_id = t.tps_id AND tj.jenis_id = p_jenis_id AND tj.diterima = 1
                JOIN jenis_sampah j      ON j.jenis_id = tj.jenis_id
               WHERE t.status = 'buka'
                 AND t.is_verified = 1
                 AND (j.kategori <> 'medis' OR t.izin_limbah_medis = 1)
                 AND (tj.kapasitas_maks_kg - tj.terpakai_kg) >= p_berat
               ORDER BY jarak_km
               LIMIT 5;
            END
        SQL);
    }

    public function down(): void
    {
        if (! $this->isMySql()) {
            return;
        }

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_rekomendasi_tps');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_selesaikan_setoran');

        DB::unprepared('DROP TRIGGER IF EXISTS trg_tjs_au');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_penjemputan_au');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_setoran_au');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_setoran_ai');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_setoran_detail_bi');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_setoran_bi');

        DB::statement('DROP VIEW IF EXISTS v_tugas_angkut');
        DB::statement('DROP VIEW IF EXISTS v_setoran_masuk_tps');
        DB::statement('DROP VIEW IF EXISTS v_kapasitas_tps');

        // Drop check constraints jika ada
        foreach ([
            ['transaksi_poin', 'chk_poin'],
            ['ulasan', 'chk_rating'],
            ['setoran_detail', 'chk_sd_berat'],
            ['tps_jenis_sampah', 'chk_tjs'],
            ['tps', 'chk_tps_kapasitas'],
        ] as [$table, $name]) {
            $exists = DB::selectOne('
                SELECT 1 FROM information_schema.TABLE_CONSTRAINTS
                 WHERE CONSTRAINT_SCHEMA = DATABASE()
                   AND TABLE_NAME = ?
                   AND CONSTRAINT_NAME = ?
            ', [$table, $name]);

            if ($exists) {
                DB::statement("ALTER TABLE `{$table}` DROP CHECK `{$name}`");
            }
        }
    }
};
