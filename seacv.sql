-- seacv.sql
CREATE DATABASE IF NOT EXISTS seacv;
USE seacv;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    category ENUM('CV Kreatif', 'CV ATS', 'Surat Lamaran Kerja', 'Surat Pengunduran diri') NOT NULL,
    image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample data
INSERT INTO products (name, price, category, image) VALUES 
('CV Kreatif Modern', 150000, 'CV Kreatif', 'uploads/cv-kreatif-modern.jpg'),
('CV Kreatif Designer', 175000, 'CV Kreatif', 'uploads/cv-kreatif-designer.jpg'),
('CV ATS Standar', 125000, 'CV ATS', 'uploads/cv-ats-standar.jpg'),
('CV ATS Professional', 150000, 'CV ATS', 'uploads/cv-ats-professional.jpg'),
('Surat Lamaran IT Support', 75000, 'Surat Lamaran Kerja', 'uploads/surat-lamaran-it.jpg'),
('Surat Lamaran Marketing', 75000, 'Surat Lamaran Kerja', 'uploads/surat-lamaran-marketing.jpg'),
('Surat Pengunduran Profesional', 50000, 'Surat Pengunduran diri', 'uploads/surat-pengunduran-profesional.jpg'),
('Surat Pengunduran Formal', 50000, 'Surat Pengunduran diri', 'uploads/surat-pengunduran-formal.jpg');