# AxisPM - Installation Guide

## **🚀 Quick Setup Instructions**

### **Prerequisites**
- **XAMPP** installed (Apache + MySQL) - [Download here](https://www.apachefriends.org/)
- **Git** (optional, for cloning)

---

## **📥 Installation Steps**

### **1. Get the Project Files**
```bash
git clone https://github.com/Kamel-CS/AxisPM.git
```
**Then move it to XAMPP's htdocs:**
- **Windows:** Move to `C:\xampp\htdocs\AxisPM`
- **Linux:** Move to `/opt/lampp/htdocs/AxisPM`

**OR**  
Download ZIP and extract directly to XAMPP's `htdocs/AxisPM` folder.

---

### **2. Set Up Database**
1. Start **XAMPP** → Run **Apache** and **MySQL**.
2. Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
3. Go to **Import** → Select the SQL file:
   ```
   AxisPM/database/tms_db.sql
   ```
4. Click **Go** → The database `tms_db` will be created automatically with:
   - Default admin: `admin@admin.com`  
   - Password: `admin`

---

### **3. Run the Application**
Open your browser and go to:
```
http://localhost/AxisPM/index.html
```

---

## **🔑 Default Login (Pre-Configured)**
- **Admin Account:**  
  📧 `admin@admin.com`  
  🔑 `admin`  

*(Change this password after first login!)*

---

## **⚙️ Key Features**
- **User Roles:** Admin, Manager, Employee  
- **Task Management:** Create, assign, track progress  
- **Basic AI Integration:** Gemini API
- **Self-Hosted:** No external servers needed  

---

## **📜 License**
MIT License - Free for educational and personal use.

**Ready to use AxisPM!** 🎉  
This keeps setup **simple and foolproof** for testers. Let me know if you need adjustments! 🛠️
