import xlsx from "xlsx";
import mysql from "mysql2/promise";
/**
 * HOW TO RUN:
 * - Make sure nodejs is installed
 * - run `node database/importExcel.js`
 */

/**
 * NOTES: ONLY LOCAL DB CONFIG!!!
 */
const dbConfig = {
  host: "127.0.0.1",
  user: "root", // Ganti dengan username MySQL
  password: "", // Ganti dengan password MySQL
  database: "anna8356_apm_db", // Ganti dengan nama database
};

const filePath = "./database/master.xlsx";

async function importExcel() {
  try {
    // Buat koneksi ke database
    const connection = await mysql.createConnection(dbConfig);

    // Baca file Excel
    const workbook = xlsx.readFile(filePath);
    const sheetNames = workbook.SheetNames;

    // Loop melalui setiap sheet/tab
    for (const sheetName of sheetNames) {
      const worksheet = workbook.Sheets[sheetName];
      const jsonData = xlsx.utils.sheet_to_json(worksheet);

      if (jsonData.length === 0) {
        console.log(`Sheet ${sheetName} kosong, dilewati.`);
        continue;
      }

      // Ambil nama tabel berdasarkan nama sheet
      const tableName = sheetName;

      // Insert data ke MySQL
      for (const row of jsonData) {
        // Ambil nama kolom dari file Excel
        const columns = Object.keys(row)
          .map((col) => `\`${col}\``)
          .join(", ");
        const placeholders = Object.keys(row)
          .map(() => "?")
          .join(", ");

        // Buat query INSERT
        const sql = `INSERT INTO \`${tableName}\` (${columns}) VALUES (${placeholders})`;
        console.log(sql);

        const values = Object.values(row).map(
          (value) => value?.toString().trim() || null
        );
        console.log("** Import data ke ", tableName, ". ", values);

        await connection.execute(sql, values);
      }

      console.log(
        `✅ Data dari sheet '${sheetName}' berhasil diimpor ke tabel '${tableName}'.`
      );
    }

    // Tutup koneksi
    await connection.end();
    console.log("🎉 Import selesai!");
  } catch (error) {
    console.error("❌ Error saat mengimpor data:", error.message);
  }
}

// Jalankan fungsi import
importExcel();
