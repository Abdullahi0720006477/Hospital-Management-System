# 🏥 MediCore Hospital OS - Intelligent Healthcare Ecosystem

![Version](https://img.shields.io/badge/version-2.4.9-blue.svg)
![Symfony](https://img.shields.io/badge/Symfony-6.4+-black.svg?logo=symfony)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4.svg?logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1.svg?logo=mysql)
![License](https://img.shields.io/badge/license-MIT-green.svg)

MediCore Hospital OS (v2.4.9 PRO) is a state-of-the-art Hospital Management System designed for modern healthcare facilities. Built with **Symfony 6.4** and **MySQL**, it bridges the gap between medical expertise and advanced AI technology through clinical intelligence, predictive analytics, and a cinematic user experience.

---

## 📸 Visual Showcase

| Public Landing Page | Admin Analytics | Doctor Dashboard |
| :--- | :--- | :--- |
| ![Landing Page](https://via.placeholder.com/800x450?text=MediCore+Landing+Page) | ![Admin Dashboard](https://via.placeholder.com/800x450?text=Admin+Analytics+Orchestration) | ![Doctor Portal](https://via.placeholder.com/800x450?text=Clinical+Intelligence+Portal) |

*Screenshots captured from the live v2.4.9 production build.*

---

## ✨ Premium Features

### 🩺 1. Clinical Intelligence (Doctor Portal)
- **NEWS2 Algorithm Integration**: Automated patient risk scoring using the National Early Warning Score 2 system.
- **Real-Time Clinical Alerts**: Instant visual indicators (Stable/Medium/High/Emergency) based on vital signs analysis.
- **Smart Patient Feed**: A data-driven approach to appointment management with priority indicators.

### 🏢 2. Facility Orchestration (Admin Portal)
- **Live Ward Occupancy**: Real-time tracking of hospital bed usage across departments (ICU, ER, General).
- **AI Bed Forecasting**: Predictive analytics that forecast bridge occupancy trends 24-48 hours in advance.
- **Department Performance**: Heatmaps showing patient inflow (OPD vs ER) and average wait times.
- **Security Audit Logs**: Comprehensive tracking of all administrative activities with IP and user metadata.

### 🧪 3. Patient Empowerment (Patient Portal)
- **Health Trend Visualization**: Dynamic Chart.js integration showing weight and Blood Pressure historical trends.
- **Digital Health Record**: Secure access to medical records, prescriptions, and diagnostic history.
- **One-Click Booking**: A frictionless appointment scheduling interface with specialty filtering.

### 🤖 4. Intelligent HMS Assistant
- A custom-built **MediBot** AI assistant available 24/7 on the public site and internal dashboards for quick navigation, pharmacy location info, and technical support.

---

## 🎨 Design Philosophy: "Cinematic Healthcare"
MediCore features a **high-end glassmorphism** design system:
- **Atmospheric Backgrounds**: Professional medical photography with light/dark overlays for high readability.
- **Deep Glassmorphism**: Advanced CSS `backdrop-filter: blur(30px)` and semi-transparent borders.
- **Motion UI**: Smooth animations powered by `Animate.css` and custom keyframes.
- **Adaptive Dark Mode**: A system-wide theme engine that automatically persists user preferences.

---

## 🚀 Quick Technical Setup

### Prerequisites
- **PHP 8.2+**
- **Composer**
- **Node.js & NPM**
- **MySQL 8.0**
- **Laragon/XAMPP**

### Installation Steps
1. **Clone the project:**
   ```bash
   git clone https://github.com/yourusername/Hospital-Mnagemnt-system.git
   cd Hospital-Mnagemnt-system
   ```
2. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```
3. **Configure Environment:**
   Edit `.env` and update your database credentials:
   ```env
   DATABASE_URL="mysql://root:@127.0.0.1:3306/hms_db?serverVersion=8.0"
   ```
4. **Initialize Database:**
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```
5. **Seed Modern Data (NEWS2 & Bed Stats):**
   ```bash
   php bin/console app:setup-test-data
   ```
6. **Launch System:**
   ```bash
   symfony serve -d
   ```

---

## 🔒 Security Protocol
- **RBAC (Role-Based Access Control)**: Granular permissions for Admin, Doctor, and Patient roles.
- **Encryption**: TLS/SSL ready configurations with Argon2ID password hashing.
- **CSRF Protection**: Native Symfony form protection on every data entry point.

---

## 📈 Roadmap (Phase 2)
- [ ] **Drug Interaction Engine**: Real-time medication cross-check against patient allergies.
- [ ] **Voice-to-EHR**: NLP-powered clinical note dictation for doctors.
- [ ] **E-Pharmacy Integration**: Automated prescription fulfillment and inventory sync.

---

## 📞 Support & Contribution
This system is an academic-first project built with passion for the healthcare industry. Contributions, issues, and feature requests are welcome.

**Developer Support**: [abdullahimukhtar717@gmail.com](mailto:abdullahimukhtar717@gmail.com)  
**Hotline**: +254 720 006 477

---
*© 2026 MediCore Hospital Management Systems. All Rights Reserved.*
