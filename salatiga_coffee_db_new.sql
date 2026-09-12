/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.14-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: salatiga_coffee_db
-- ------------------------------------------------------
-- Server version	10.11.14-MariaDB-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cafes`
--

DROP TABLE IF EXISTS `cafes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cafes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) DEFAULT NULL,
  `lokasi` varchar(100) DEFAULT NULL,
  `lokasi_filter` varchar(50) DEFAULT NULL,
  `rating` float DEFAULT NULL,
  `total_ulasan` int(11) DEFAULT NULL,
  `harga_min` int(11) DEFAULT NULL,
  `harga_estimasi` varchar(50) DEFAULT NULL,
  `gambar` text DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `alamat_lengkap` text DEFAULT NULL,
  `kontak_ig` varchar(50) DEFAULT NULL,
  `kontak_wa` varchar(20) DEFAULT NULL,
  `fitur` text DEFAULT NULL,
  `promo` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cafes`
--

LOCK TABLES `cafes` WRITE;
/*!40000 ALTER TABLE `cafes` DISABLE KEYS */;
INSERT INTO `cafes` VALUES
(1,'BrewCraft Salatiga','Sidorejo, Salatiga','sidorejo',4.8,1,15000,'Rp 15.000 - Rp 35.000','https://images.unsplash.com/photo-1554118811-1e0d58224f24','Konsep semi-outdoor yang sejuk. Cocok untuk nugas dan kopi pilihan nusantara.','Jl. Diponegoro No. 45, Salatiga','@brewcraft.sltg','+6281234567890','Bisa QRIS,Wi-Fi Cepat,Outdoor',1),
(2,'Senja Coffee Hub','Tingkir, Salatiga','tingkir',4.6,89,20000,'Rp 20.000 - Rp 45.000','https://images.unsplash.com/photo-1493857671505-72967e2e2760','Tempat nongkrong asik dengan view senja dan alunan musik akustik.','Jl. Tingkir Raya No. 10, Salatiga','@senjacoffee.hub','+6285712345678','Live Music,Bisa QRIS,Smoking Area',0),
(3,'Ruang Rasa Roastery','Argomulyo, Salatiga','argomulyo',4.9,210,18000,'Rp 18.000 - Rp 40.000','https://images.unsplash.com/photo-1509042239860-f550ce710b93','Roastery profesional dengan bean pilihan dari seluruh Indonesia.','Kawasan Industri Argomulyo','@ruangrasaroastery','+6281311112222','AC,Co-working Space,High Speed WiFi',1),
(4,'Kopi Titik Koma','Sidomukti, Salatiga','sidomukti',4.5,65,12000,'Rp 12.000 - Rp 25.000','https://images.unsplash.com/photo-1600093463592-8e36ae95ef56','Tempat kopi 24 jam untuk pejuang begadang di Salatiga.','Jl. Sidomukti Utama No. 8','@titikkoma.sltg','+6281299998888','24 Jam,Wi-Fi,Colokan Banyak',0),
(5,'Pancarona Coffee','Sidorejo, Salatiga','sidorejo',4.7,156,20000,'Rp 20.000 - Rp 50.000','https://images.unsplash.com/photo-1525610553991-2bede1a236e2','Cafe dengan rooftop kece dan ramah hewan peliharaan.','Jl. Pemuda No. 77, Salatiga','@pancarona.coffee','+6281255554444','Rooftop,Pet Friendly,Bisa QRIS',1),
(6,'Lokalitas Kedai Kopi','Tingkir, Salatiga','tingkir',4.4,42,10000,'Rp 10.000 - Rp 20.000','https://images.unsplash.com/photo-1514432324607-a1252c94d8f6','Sederhana, merakyat, dan kopinya sangat lokal.','Jl. Tingkir Tengah No. 5','@lokalitas.kedai','+6282100007777','Outdoor,Smoking Area',0),
(8,'Imam Cafe Magelangan','Sidomukti','sidomukti',4.7,6,20000,'50000','','wkwk',NULL,NULL,NULL,NULL,0);
/*!40000 ALTER TABLE `cafes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `diskon`
--

DROP TABLE IF EXISTS `diskon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `diskon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode` varchar(50) NOT NULL,
  `judul` varchar(100) NOT NULL,
  `potongan` varchar(50) NOT NULL,
  `deskripsi` text NOT NULL,
  `min_beli` varchar(50) NOT NULL,
  `valid_hingga` varchar(50) NOT NULL,
  `ikon` varchar(50) DEFAULT 'fas fa-tag',
  `badge` varchar(50) DEFAULT 'Promo',
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode` (`kode`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `diskon`
--

LOCK TABLES `diskon` WRITE;
/*!40000 ALTER TABLE `diskon` DISABLE KEYS */;
INSERT INTO `diskon` VALUES
(1,'MABACOFFEE','Diskon Khusus Mahasiswa Baru','Potongan 25%','Spesial buat kamu mahasiswa baru di Salatiga.','Min. Belanja Rp 30.000','31 Agustus 2026','fas fa-user-graduate','Khusus Mahasiswa'),
(2,'QRISHEMAT','Promo Cashless BI (QRIS)','Potongan Rp 10.000','Lebih hemat untuk transaksi menggunakan QRIS.','Min. Belanja Rp 40.000','15 Juli 2026','fas fa-qrcode','Paling Laris'),
(3,'SENJAKOPI','Voucher Senja Produktif','Potongan 15%','Nugas ditemani kopi favorit makin produktif.','Tanpa Min. Belanja','30 Juni 2026','fas fa-cloud-moon','Sisa 3 Hari'),
(4,'COFFEEHUB20','Diskon Pengguna Baru','Diskon 20%','Potongan harga untuk pesanan pertamamu.','Min. Belanja Rp 25.000','31 Desember 2026','fas fa-star','Pengguna Baru');
/*!40000 ALTER TABLE `diskon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cafe_id` int(11) DEFAULT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `harga` int(11) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `img` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cafe_id` (`cafe_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES
(1,1,'Kopi','Cappuccino Classic',25000,'Espresso dengan susu steam lembut.','https://images.unsplash.com/photo-1572442388796-11668a67e53d'),
(2,1,'Snack','Croissant Butter',22000,'Pastry renyah.','https://images.unsplash.com/photo-1555507036-ab1f4038808a'),
(3,2,'Kopi','Es Kopi Susu Senja',18000,'Signature kopi manis.','https://images.unsplash.com/photo-1590781283653-3b0d9bae7c77'),
(4,2,'Snack','Singkong Keju',15000,'Cemilan tradisional.','https://images.unsplash.com/photo-1582235478144-a63690629ec2'),
(5,3,'Manual Brew','V60 Gayo',35000,'Biji kopi single origin.','https://images.unsplash.com/photo-1442512595333-e8910647e985'),
(6,4,'Kopi','Kopi Hitam',10000,'Kopi tubruk murni.','https://images.unsplash.com/photo-1541167760496-1628856ab779'),
(7,5,'Non-Kopi','Red Velvet',25000,'Creamy & sweet.','https://images.unsplash.com/photo-1620751989473-b3c66fdf0dc5'),
(8,6,'Kopi','Kopi Susu Gula Aren',15000,'Manis aren alami.','https://images.unsplash.com/photo-1592641616047-925287f61c94');
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservasi`
--

DROP TABLE IF EXISTS `reservasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservasi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cafe_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nama_pemesan` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `tanggal` date NOT NULL,
  `jam` varchar(10) NOT NULL,
  `jumlah_orang` int(11) NOT NULL DEFAULT 1,
  `catatan` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Menunggu',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `cafe_id` (`cafe_id`),
  CONSTRAINT `reservasi_ibfk_1` FOREIGN KEY (`cafe_id`) REFERENCES `cafes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservasi`
--

LOCK TABLES `reservasi` WRITE;
/*!40000 ALTER TABLE `reservasi` DISABLE KEYS */;
INSERT INTO `reservasi` VALUES
(1,3,NULL,'zia','11111','2026-10-09','21:00',5,'dekat ac','Dikonfirmasi','2026-06-15 12:27:46'),
(2,3,NULL,'ajeng','2222','2027-03-02','22:00',3,'','Dikonfirmasi','2026-06-15 13:44:12');
/*!40000 ALTER TABLE `reservasi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaksi`
--

DROP TABLE IF EXISTS `transaksi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `tanggal` datetime NOT NULL DEFAULT current_timestamp(),
  `total` int(11) NOT NULL,
  `metode_pembayaran` varchar(50) NOT NULL,
  `status` varchar(20) DEFAULT 'Selesai',
  `detail_items` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaksi`
--

LOCK TABLES `transaksi` WRITE;
/*!40000 ALTER TABLE `transaksi` DISABLE KEYS */;
INSERT INTO `transaksi` VALUES
(1,1,'SCH-5346810000','2026-06-11 02:50:03',130900,'QRIS','Selesai','[{\"nama\":\"Cappuccino Classic\",\"harga\":25000,\"img\":\"https://images.unsplash.com/photo-1572442388796-11668a67e53d\",\"qty\":3},{\"nama\":\"Croissant Butter\",\"harga\":22000,\"img\":\"https://images.unsplash.com/photo-1555507036-ab1f4038808a\",\"qty\":2}]'),
(2,6,'SCH-3437510000','2026-06-15 12:02:18',27500,'QRIS','Selesai','[{\"nama\":\"Cappuccino Classic\",\"harga\":25000,\"img\":\"https://images.unsplash.com/photo-1572442388796-11668a67e53d\",\"qty\":1}]'),
(3,6,'SCH-4672510000','2026-06-15 12:07:27',27500,'QRIS','Selesai','[{\"nama\":\"Cappuccino Classic\",\"harga\":25000,\"img\":\"https://images.unsplash.com/photo-1572442388796-11668a67e53d\",\"qty\":1}]'),
(4,6,'SCH-529610000','2026-06-15 12:09:47',24200,'QRIS','Selesai','[{\"nama\":\"Croissant Butter\",\"harga\":22000,\"img\":\"https://images.unsplash.com/photo-1555507036-ab1f4038808a\",\"qty\":1}]'),
(5,6,'SCH-2011810000','2026-06-15 12:12:03',24200,'QRIS','Selesai','[{\"nama\":\"Croissant Butter\",\"harga\":22000,\"img\":\"https://images.unsplash.com/photo-1555507036-ab1f4038808a\",\"qty\":1}]');
/*!40000 ALTER TABLE `transaksi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ulasan`
--

DROP TABLE IF EXISTS `ulasan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ulasan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cafe_id` int(11) NOT NULL,
  `nama_user` varchar(100) NOT NULL,
  `rating` decimal(2,1) NOT NULL,
  `komentar` text DEFAULT NULL,
  `tanggal` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cafe_id` (`cafe_id`),
  CONSTRAINT `ulasan_ibfk_1` FOREIGN KEY (`cafe_id`) REFERENCES `cafes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ulasan`
--

LOCK TABLES `ulasan` WRITE;
/*!40000 ALTER TABLE `ulasan` DISABLE KEYS */;
INSERT INTO `ulasan` VALUES
(1,1,'Imam Adi',5.0,'haha','2026-06-15');
/*!40000 ALTER TABLE `ulasan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(10) DEFAULT 'user',
  `cafe_id` int(11) DEFAULT NULL,
  `poin` int(11) DEFAULT 0,
  `pesanan_selesai` int(11) DEFAULT 0,
  `member_sejak` varchar(50) DEFAULT 'Juni 2026',
  `alamat` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Imam Adi','imamadi@gmail.com','$2y$10$P59vm4SugaEp8pEBJ1u7QuWf0wu5RTJusrPoZ4cElywuj4Q/DtuUW','user',NULL,50,1,'Juni 2026','Jalan Mangunsari No. 597B, Salatiga'),
(2,'Admin Website','admin@salatiga.coffee','$2y$10$FaLBj1jpcpbxpewiptyX9OGQe24FETEKnGP7GOBjzdoTkZ5fjrh06','admin',NULL,0,0,'Juni 2026','Salatiga'),
(3,'Kasir BrewCraft','kasir.brewcraft@salatiga.coffee','$2y$10$lXQBAd4h5f8NoLj3f0uAa.GV8ES3EulSByZozNGdonG008CSEyiSm','kasir',1,0,0,'Juni 2026',NULL),
(4,'Kasir Senja Banget','kasir.senja@salatiga.coffee','$2y$10$lXQBAd4h5f8NoLj3f0uAa.GV8ES3EulSByZozNGdonG008CSEyiSm','kasir',2,0,0,'Juni 2026',NULL),
(5,'Kasir Ruang Rasa','kasir.ruangrasa@salatiga.coffee','$2y$10$lXQBAd4h5f8NoLj3f0uAa.GV8ES3EulSByZozNGdonG008CSEyiSm','kasir',3,0,0,'Juni 2026',NULL),
(6,'imamganteng','imamggs@gmail.com','$2y$10$Dq4lGqBoARqamhj9nQzrc.ZOxkx5K2Qn2wFsp8ZXT8dOPKR6D36iW','user',NULL,200,4,'Juni 2026',''),
(7,'Kasir Imam Cafe Magelangan','imamcafemagelangan@gmail.com','$2y$10$0lKNQMRQmBEHrvW9GpbNwuc6fctX6rqNGrCb0CRLfobDNtuWBEnlC','kasir',8,0,0,'Juni 2026',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-17 15:57:07
