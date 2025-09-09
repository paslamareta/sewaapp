<script setup lang="ts">
import { jsPDF } from "jspdf";
import autoTable from "jspdf-autotable";
import ExcelJS from "exceljs";
import { saveAs } from "file-saver";
import { computed } from "vue";
import AppLayout from "@/layouts/AppLayout.vue";
import { Users, AppWindow, Clock, AlertCircle, FileDown, FileSpreadsheet } from "lucide-vue-next";

// --- Definisi tipe data ---
interface Application {
  id: number;
  name: string;
}

interface SewaApp {
  id: number;
  application: Application;
}

interface Customer {
  id: number;
  name: string;
  sewa_aplikasi: SewaApp[];
}

interface Stats {
  customers: number;
  applications: number;
  activeSewa: number;
  expiredSewa: number;
}

interface ExpiredSewa {
  id: number;
  customer: { name: string };
  application: { name: string };
  tanggal_expired: string;
}

// --- Props ---
const props = defineProps<{
  stats: Stats;
  expiredSewaData: ExpiredSewa[];
  sewaPerApp: Customer[];
}>();

// Flatten sewaPerApp agar loop tabel lebih rapi
const flatSewaPerApp = computed(() =>
  props.sewaPerApp.flatMap((cust: Customer) =>
    cust.sewa_aplikasi.map((app: SewaApp) => ({
      id: `${cust.id}-${app.id}`,
      customer: cust.name,
      application: app.application?.name,
      total: 1,
    }))
  )
);

// Export ke PDF
const exportPDF = (expiredSewaData: ExpiredSewa[], sewaPerApp: any[]) => {
  const doc = new jsPDF();

  // Expired Rentals
  doc.text("Expired Rentals Report", 14, 15);
  autoTable(doc, {
    head: [["Customer", "Application", "Tanggal Expired"]],
    body: expiredSewaData.map(r => [
      r.customer?.name ?? "-",
      r.application?.name ?? "-",
      r.tanggal_expired,
    ]),
    startY: 20,
  });

  // Jumlah Sewa Per Aplikasi
  doc.text("Jumlah Sewa Per Aplikasi", 14, (doc as any).lastAutoTable.finalY + 15);
  autoTable(doc, {
    head: [["Customer", "Application", "Total Sewa"]],
    body: sewaPerApp.map(row => [
      row.customer,
      row.application,
      row.total,
    ]),
    startY: (doc as any).lastAutoTable.finalY + 20,
  });

  doc.save("report.pdf");
};

// Export ke Excel
const exportExcel = async (expiredSewaData: ExpiredSewa[], sewaPerApp: any[]) => {
  const workbook = new ExcelJS.Workbook();

  // Worksheet 1: Expired Rentals
  const sheet1 = workbook.addWorksheet("Expired Rentals");
  sheet1.addRow(["Customer", "Application", "Tanggal Expired"]);
  expiredSewaData.forEach(r => {
    sheet1.addRow([
      r.customer?.name ?? "-",
      r.application?.name ?? "-",
      r.tanggal_expired,
    ]);
  });

  // Worksheet 2: Jumlah Sewa Per Aplikasi
  const sheet2 = workbook.addWorksheet("Sewa Per App");
  sheet2.addRow(["Customer", "Application", "Total Sewa"]);
  sewaPerApp.forEach(row => {
    sheet2.addRow([row.customer, row.application, row.total]);
  });

  const buffer = await workbook.xlsx.writeBuffer();
  saveAs(new Blob([buffer]), "report.xlsx");
};
</script>

<template>
  <AppLayout title="Dashboard">
    <div class="p-6 space-y-10">
      <!-- Header -->
      <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight"> Dashboard</h1>

      <!-- Stats -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl shadow hover:scale-105 transition">
          <div class="flex items-center gap-3">
            <Users class="w-6 h-6 text-blue-600" />
            <p class="text-sm text-gray-600">Customers</p>
          </div>
          <p class="text-3xl font-bold text-blue-700 mt-2">{{ props.stats.customers }}</p>
        </div>

        <div class="p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-2xl shadow hover:scale-105 transition">
          <div class="flex items-center gap-3">
            <AppWindow class="w-6 h-6 text-green-600" />
            <p class="text-sm text-gray-600">Applications</p>
          </div>
          <p class="text-3xl font-bold text-green-700 mt-2">{{ props.stats.applications }}</p>
        </div>

        <div class="p-6 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-2xl shadow hover:scale-105 transition">
          <div class="flex items-center gap-3">
            <Clock class="w-6 h-6 text-yellow-600" />
            <p class="text-sm text-gray-600">Active Rentals</p>
          </div>
          <p class="text-3xl font-bold text-yellow-700 mt-2">{{ props.stats.activeSewa }}</p>
        </div>

        <div class="p-6 bg-gradient-to-br from-red-50 to-red-100 rounded-2xl shadow hover:scale-105 transition">
          <div class="flex items-center gap-3">
            <AlertCircle class="w-6 h-6 text-red-600" />
            <p class="text-sm text-gray-600">Expired Rentals</p>
          </div>
          <p class="text-3xl font-bold text-red-700 mt-2">{{ props.stats.expiredSewa }}</p>
        </div>
      </div>

      <!-- Table Expired Rentals -->
      <div class="bg-white p-6 rounded-2xl shadow">
        <h2 class="text-xl font-semibold mb-4 text-gray-700"> Expired Rentals</h2>
        <div class="overflow-x-auto">
          <table class="table-auto w-full border-collapse">
            <thead>
              <tr class="bg-gray-100 text-gray-700">
                <th class="border px-4 py-2 text-left">Customer</th>
                <th class="border px-4 py-2 text-left">Application</th>
                <th class="border px-4 py-2 text-left">Tanggal Expired</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="r in props.expiredSewaData"
                :key="r.id"
                class="odd:bg-gray-50 even:bg-white hover:bg-blue-50 transition"
              >
                <td class="border px-4 py-2">{{ r.customer?.name }}</td>
                <td class="border px-4 py-2">{{ r.application?.name }}</td>
                <td class="border px-4 py-2">{{ r.tanggal_expired }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Jumlah Sewa Per Aplikasi -->
      <div class="bg-white p-6 rounded-2xl shadow">
        <h2 class="text-xl font-semibold mb-4 text-gray-700">
           Jumlah Sewa Per Aplikasi dari Customers
        </h2>
        <div class="overflow-x-auto">
          <table class="table-auto w-full border-collapse">
            <thead>
              <tr class="bg-gray-100 text-gray-700">
                <th class="border px-4 py-2 text-left">Customer</th>
                <th class="border px-4 py-2 text-left">Application</th>
                <th class="border px-4 py-2 text-left">Total Sewa</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="row in flatSewaPerApp"
                :key="row.id"
                class="odd:bg-gray-50 even:bg-white hover:bg-green-50 transition"
              >
                <td class="border px-4 py-2">{{ row.customer }}</td>
                <td class="border px-4 py-2">{{ row.application }}</td>
                <td class="border px-4 py-2">{{ row.total }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Export Buttons -->
      <div class="flex gap-4">
        <button
          @click="exportPDF(props.expiredSewaData, flatSewaPerApp)"
          class="flex items-center gap-2 px-5 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg shadow"
        >
          <FileDown class="w-5 h-5" /> Export PDF
        </button>
        <button
          @click="exportExcel(props.expiredSewaData, flatSewaPerApp)"
          class="flex items-center gap-2 px-5 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg shadow"
        >
          <FileSpreadsheet class="w-5 h-5" /> Export Excel
        </button>
      </div>
    </div>
  </AppLayout>
</template>