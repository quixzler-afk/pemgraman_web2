-- ALTHEA Payroll - tambahan tabel pada database ALTHEA HR (db_kepegawaian_althea).
-- AMAN dijalankan pada database HR yang sudah ada: tidak mengubah/menghapus tabel HR, hanya MENAMBAH tabel payroll_*.
-- Import via phpMyAdmin pada database HR yang sama (pilih database lebih dulu, lalu tab Import).
-- Seluruh nilai gaji/komponen di bawah adalah DATA CONTOH (asumsi); sesuaikan dengan kebijakan resmi RS Althea.
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS payroll_komponen (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode VARCHAR(20) NOT NULL UNIQUE,
  nama VARCHAR(100) NOT NULL,
  jenis ENUM('pendapatan','potongan') NOT NULL,
  -- tetap: nilai Rp per periode | persen: nilai % dari (gaji pokok + tunjangan jabatan) | harian: Rp x hari hadir (termasuk terlambat)
  -- per_terlambat: Rp x kali terlambat | per_absen: Rp x hari tidak hadir | per_izin: Rp x hari izin
  tipe ENUM('tetap','persen','harian','per_terlambat','per_absen','per_izin') NOT NULL DEFAULT 'tetap',
  nilai DECIMAL(14,2) NOT NULL DEFAULT 0,
  urutan INT NOT NULL DEFAULT 10,
  keterangan VARCHAR(255) NULL,
  status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payroll_pegawai (
  pegawai_id INT NOT NULL PRIMARY KEY,
  gaji_pokok DECIMAL(14,2) NOT NULL DEFAULT 0,
  tunjangan_jabatan DECIMAL(14,2) NOT NULL DEFAULT 0,
  bank VARCHAR(40) NULL,
  no_rekening VARCHAR(40) NULL,
  status_ptkp VARCHAR(10) NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_ppeg_pegawai FOREIGN KEY (pegawai_id) REFERENCES pegawai(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payroll_periode (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tahun SMALLINT NOT NULL,
  bulan TINYINT NOT NULL,
  tanggal_awal DATE NOT NULL,
  tanggal_akhir DATE NOT NULL,
  tanggal_bayar DATE NULL,
  status ENUM('draft','final','dibayar') NOT NULL DEFAULT 'draft',
  catatan VARCHAR(255) NULL,
  dibuat_oleh INT NULL,
  dihitung_pada DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_periode (tahun, bulan)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payroll_penyesuaian (
  id INT AUTO_INCREMENT PRIMARY KEY,
  periode_id INT NOT NULL,
  pegawai_id INT NOT NULL,
  nama VARCHAR(100) NOT NULL,
  jenis ENUM('pendapatan','potongan') NOT NULL,
  jumlah DECIMAL(14,2) NOT NULL DEFAULT 0,
  keterangan VARCHAR(150) NULL,
  created_by INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_adj (periode_id, pegawai_id),
  CONSTRAINT fk_adj_periode FOREIGN KEY (periode_id) REFERENCES payroll_periode(id) ON DELETE CASCADE,
  CONSTRAINT fk_adj_pegawai FOREIGN KEY (pegawai_id) REFERENCES pegawai(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payroll_slip (
  id INT AUTO_INCREMENT PRIMARY KEY,
  periode_id INT NOT NULL,
  pegawai_id INT NOT NULL,
  nomor VARCHAR(30) NOT NULL UNIQUE,
  nip VARCHAR(30) NOT NULL,
  nama VARCHAR(120) NOT NULL,
  jabatan VARCHAR(100) NOT NULL,
  unit VARCHAR(100) NOT NULL,
  jabatan_id INT NULL,
  unit_id INT NULL,
  bank VARCHAR(40) NULL,
  no_rekening VARCHAR(40) NULL,
  hari_jadwal INT NOT NULL DEFAULT 0,
  hadir INT NOT NULL DEFAULT 0,
  terlambat INT NOT NULL DEFAULT 0,
  izin INT NOT NULL DEFAULT 0,
  sakit INT NOT NULL DEFAULT 0,
  cuti INT NOT NULL DEFAULT 0,
  tidak_hadir INT NOT NULL DEFAULT 0,
  gaji_pokok DECIMAL(14,2) NOT NULL DEFAULT 0,
  tunjangan_jabatan DECIMAL(14,2) NOT NULL DEFAULT 0,
  total_pendapatan DECIMAL(14,2) NOT NULL DEFAULT 0,
  total_potongan DECIMAL(14,2) NOT NULL DEFAULT 0,
  gaji_bersih DECIMAL(14,2) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_slip (periode_id, pegawai_id),
  INDEX idx_slip_pegawai (pegawai_id),
  CONSTRAINT fk_slip_periode FOREIGN KEY (periode_id) REFERENCES payroll_periode(id) ON DELETE CASCADE,
  CONSTRAINT fk_slip_pegawai FOREIGN KEY (pegawai_id) REFERENCES pegawai(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payroll_slip_item (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slip_id INT NOT NULL,
  komponen_id INT NULL,
  kode VARCHAR(20) NULL,
  nama VARCHAR(100) NOT NULL,
  jenis ENUM('pendapatan','potongan') NOT NULL,
  jumlah DECIMAL(14,2) NOT NULL DEFAULT 0,
  keterangan VARCHAR(150) NULL,
  urutan INT NOT NULL DEFAULT 10,
  INDEX idx_item_slip (slip_id),
  CONSTRAINT fk_item_slip FOREIGN KEY (slip_id) REFERENCES payroll_slip(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===== DATA AWAL (CONTOH) =====
INSERT IGNORE INTO payroll_komponen (id, kode, nama, jenis, tipe, nilai, urutan, keterangan) VALUES
(1,'UM','Uang Makan','pendapatan','harian',25000,10,'Per hari hadir (termasuk terlambat)'),
(2,'UT','Uang Transport','pendapatan','harian',20000,20,'Per hari hadir (termasuk terlambat)'),
(3,'TKS','Tunjangan Kesehatan RS','pendapatan','tetap',150000,30,'Tetap per periode'),
(4,'BPJSK','BPJS Kesehatan (1%)','potongan','persen',1,40,'1% dari gaji pokok + tunjangan jabatan'),
(5,'JHT','BPJS Ketenagakerjaan JHT (2%)','potongan','persen',2,50,'2% dari gaji pokok + tunjangan jabatan'),
(6,'PTL','Potongan Keterlambatan','potongan','per_terlambat',25000,60,'Per kali terlambat'),
(7,'PTH','Potongan Tidak Hadir','potongan','per_absen',150000,70,'Per hari tidak hadir tanpa keterangan'),
(8,'PIZ','Potongan Izin','potongan','per_izin',50000,80,'Per hari izin (sakit dan cuti tidak dipotong)');

INSERT IGNORE INTO payroll_pegawai (pegawai_id, gaji_pokok, tunjangan_jabatan, bank, no_rekening, status_ptkp)
SELECT p.id,
  CASE p.jabatan_id WHEN 1 THEN 12000000 WHEN 2 THEN 5500000 WHEN 3 THEN 5200000 WHEN 4 THEN 7000000 WHEN 5 THEN 4500000 WHEN 6 THEN 5000000 WHEN 7 THEN 5200000 ELSE 4500000 END,
  CASE p.jabatan_id WHEN 1 THEN 3000000 WHEN 2 THEN 800000 WHEN 3 THEN 700000 WHEN 4 THEN 1200000 WHEN 5 THEN 500000 WHEN 6 THEN 600000 WHEN 7 THEN 600000 ELSE 500000 END
    + (CASE WHEN EXISTS (SELECT 1 FROM unit_kerja u WHERE u.kepala_unit_id = p.id) THEN 1500000 ELSE 0 END),
  CASE MOD(p.id, 3) WHEN 0 THEN 'BCA' WHEN 1 THEN 'Mandiri' ELSE 'BNI' END,
  CONCAT('88', LPAD(p.id, 8, '0')),
  CASE MOD(p.id, 4) WHEN 0 THEN 'K/1' WHEN 1 THEN 'TK/0' WHEN 2 THEN 'K/0' ELSE 'TK/0' END
FROM pegawai p WHERE p.status <> 'nonaktif';

-- Tiga periode contoh (draft). Buka menu Periode lalu tekan "Hitung" (atau "Hitung semua draft").
INSERT IGNORE INTO payroll_periode (tahun, bulan, tanggal_awal, tanggal_akhir, tanggal_bayar, status)
SELECT YEAR(d), MONTH(d), d, LAST_DAY(d), DATE_ADD(LAST_DAY(d), INTERVAL 1 DAY), 'draft'
FROM (SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 2 MONTH), '%Y-%m-01') AS d
      UNION ALL SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01')
      UNION ALL SELECT DATE_FORMAT(CURDATE(), '%Y-%m-01')) t;
