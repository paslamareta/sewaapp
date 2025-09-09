<script setup lang="ts">
import { ref, onMounted } from "vue";
import { Head } from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Toast from "primevue/toast";
import { useToast } from "primevue/usetoast";
import MultiSelect from "primevue/multiselect";

const toast = useToast();
const broadcasts = ref<any[]>([]);
const selectedEmails = ref<string[]>([]);
const customers = ref<any[]>([]);
const message = ref("");
const loading = ref(false); 

// Ambil CSRF token 
const csrfToken = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content;

//  Ambil data broadcast
async function loadBroadcasts() {
  const res = await fetch("/api/broadcasts", {
    headers: { "X-Requested-With": "XMLHttpRequest" },
    credentials: "include",
  });
  const data = await res.json();
  broadcasts.value = data.items;
}

// Ambil data customers
async function loadCustomers() {
  const res = await fetch("/api/customers/all", {
    headers: { "X-Requested-With": "XMLHttpRequest" },
    credentials: "include",
  });
  const data = await res.json();
  customers.value = data.items;
}

//Kirim broadcast
async function sendBroadcast() {
  if (!selectedEmails.value.length || !message.value) {
    toast.add({
      severity: "warn",
      summary: "Perhatian",
      detail: "Pilih email tujuan dan isi pesan",
      life: 3000,
    });
    return;
  }

  loading.value = true; 
  try {
    const res = await fetch("/api/broadcasts", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrfToken ?? "",
        "X-Requested-With": "XMLHttpRequest",
      },
      credentials: "include",
      body: JSON.stringify({
        emails: selectedEmails.value,
        message: message.value,
      }),
    });

    if (res.ok) {
      toast.add({
        severity: "success",
        summary: "Berhasil",
        detail: "Broadcast terkirim!",
        life: 3000,
      });
      selectedEmails.value = [];
      message.value = "";
      loadBroadcasts();
    } else {
      const err = await res.json().catch(() => ({}));
      toast.add({
        severity: "error",
        summary: "Error",
        detail: err.message ?? "Gagal mengirim broadcast",
        life: 3000,
      });
    }
  } finally {
    loading.value = false; 
  }
}

onMounted(() => {
  loadBroadcasts();
  loadCustomers();
});
</script>

<template>
  <AppLayout>
    <Head title="Broadcast Email" />
    <Toast />

    <div class="p-6 space-y-6">
      <!-- Form -->
      <div class="bg-white p-6 rounded shadow">
        <h2 class="font-bold text-xl mb-4">Kirim Broadcast Email</h2>
        <p class="text-sm text-gray-600 mb-4">
          Pilih <b>customers aktif</b> yang ingin dikirimi pesan.
        </p>

        <!-- Pilih email -->
        <div class="mb-4">
          <label class="block mb-1 font-medium">Pilih Email Customers</label>
          <MultiSelect
            v-model="selectedEmails"
            :options="customers"
            optionLabel="email"
            optionValue="email"
            placeholder="Pilih email tujuan"
            display="chip"
            class="w-full"
          />
        </div>

        <!-- Pesan -->
        <div class="mb-4">
          <label class="block mb-1 font-medium">Message</label>
          <textarea
            v-model="message"
            rows="6"
            class="w-full border rounded p-2"
            placeholder="Isi pesan broadcast"
          ></textarea>
        </div>

        <!-- Tombol kirim -->
        <Button
          label="Kirim Broadcast"
          icon="pi pi-send"
          severity="success"
          :loading="loading"
          @click="sendBroadcast"
        />
      </div>

      <!-- Riwayat -->
      <div class="bg-white p-6 rounded shadow">
        <h2 class="font-bold text-xl mb-4">Riwayat Broadcast</h2>
        <DataTable :value="broadcasts" dataKey="id" class="p-datatable-sm">
          <Column field="emails" header="Dikirim pada" />
        </DataTable>
      </div>
    </div>
  </AppLayout>
</template>