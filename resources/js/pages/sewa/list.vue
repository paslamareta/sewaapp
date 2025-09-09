<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import HeadingSmall from '@/components/HeadingSmall.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Divider from 'primevue/divider';
import Toast from 'primevue/toast';
import ConfirmDialog from 'primevue/confirmdialog';
import { useForm } from 'laravel-precognition-vue';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';

const toast = useToast();
const confirm = useConfirm();

const breadcrumbItems = [{ title: 'Sewa Aplikasi', href: '/sewa' }];

const datas = ref<any[]>([]);
const totalRecords = ref(0);
const loading = ref(false);
const search = ref('');
const editingId = ref<number | null>(null);

// dropdown data
const customers = ref<any[]>([]);
const applications = ref<any[]>([]);
const hostings = ref<any[]>([]);

let form = useForm('post', '/api/sewa', {
  customer_id: '',
  application_id: '',
  hosting_id: '',
  domain: '',
  biaya: '',
  tanggal_mulai: '',
  tanggal_expired: '',
  status: 'Belum Aktif',
});

const submit = () =>
  form.submit()
    .then(() => {
      form.reset();
      editingId.value = null;
      loadData(dtParams.value);
      toast.add({ severity: 'success', summary: 'Success', detail: 'Operation completed successfully', life: 3000 });
    })
    .catch(() => {
      toast.add({ severity: 'error', summary: 'Error', detail: 'An error occurred while processing your request', life: 3000 });
    });

const dtParams = ref({ first: 0, rows: 10, sortField: 'tanggal_mulai', sortOrder: 1 });

// --- CRUD ---
function getCookie(name: string) {
  const m = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/([.$?*|{}()\[\]\\/+^])/g, '\\$1') + '=([^;]*)'));
  return m ? decodeURIComponent(m[1]) : '';
}

const deleteData = async (row: any) => {
  const csrfToken = getCookie('XSRF-TOKEN') || getCookie('csrf_token') || '';
  confirm.require({
    message: 'Yakin mau hapus sewa aplikasi ini?',
    header: 'Konfirmasi Hapus',
    group: 'templating',
    rejectProps: { icon: 'pi pi-times', label: 'Batal', outlined: true },
    acceptProps: { icon: 'pi pi-check', label: 'Hapus' },
    accept: () => {
      fetch(`/api/sewa/${row.id}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'X-XSRF-TOKEN': csrfToken,
        },
        credentials: 'include'
      })
        .then(res => {
          if (!res.ok) throw new Error('Failed to delete');
          return res.json();
        })
        .then(() => {
          loadData(dtParams.value);
          toast.add({ severity: 'success', summary: 'Success', detail: 'Data berhasil dihapus', life: 3000 });
        })
        .catch(() => {
          toast.add({ severity: 'error', summary: 'Error', detail: 'Gagal menghapus data', life: 3000 });
        });
    }
  });
};

async function openEditForm(row: any) {
  editingId.value = row.id;
  form = useForm('put', `/api/sewa/${row.id}`, {
    customer_id: row.customer_id,
    application_id: row.application_id,
    hosting_id: row.hosting_id,
    domain: row.domain,
    biaya: row.biaya,
    tanggal_mulai: row.tanggal_mulai,
    tanggal_expired: row.tanggal_expired,
    status: row.status,
  });
}

// --- Load Data ---
async function loadData(params: any) {
  loading.value = true;
  try {
    dtParams.value = { ...dtParams.value, ...params };
    const sortField = dtParams.value.sortField || 'tanggal_mulai';
    const sortOrder = dtParams.value.sortOrder > 0 ? 'asc' : 'desc';
    const res = await fetch(`/api/sewa?skip=${dtParams.value.first}&take=${dtParams.value.rows}&sortField=${sortField}&sortOrder=${sortOrder}&search=${encodeURIComponent(search.value)}`);
    const data = await res.json();
    datas.value = data.items;
    totalRecords.value = data.total;
  } finally {
    loading.value = false;
  }
}

function onSearch() {
  loadData({ first: 0 });
}

function onPage(event: any) {
  loadData({
    first: event.first,
    rows: event.rows,
    sortField: event.sortField || dtParams.value.sortField,
    sortOrder: event.sortOrder || dtParams.value.sortOrder
  });
}

function onSort(event: any) {
  loadData({ sortField: event.sortField, sortOrder: event.sortOrder });
}

// --- Load dropdown data ---
async function loadDropdowns() {
  const customerRes = await (await fetch('/api/customers')).json();
  customers.value = customerRes.items;

  const appRes = await (await fetch('/api/applications')).json();
  applications.value = appRes.items;

  const hostingRes = await (await fetch('/api/hostings')).json();
  hostings.value = hostingRes.items;
}

onMounted(() => {
  loadData(dtParams.value);
  loadDropdowns();
});
</script>

<template>
  <Toast />
  <ConfirmDialog group="templating">
    <template #message="slotProps">
      <div class="flex flex-col items-center w-full gap-4 border-b border-surface-200 dark:border-surface-700">
        <i :class="slotProps.message.icon" class="!text-6xl text-primary-500"></i>
        <p>{{ slotProps.message.message }}</p>
      </div>
    </template>
  </ConfirmDialog>

  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head title="Sewa Aplikasi" />
    <div class="flex h-full flex-col gap-4 p-4 overflow-x-auto">
      <div class="space-y-4">
        <!-- Header -->
        <HeadingSmall title="Sewa Aplikasi" description="Kelola sewa aplikasi" />
        <Divider />

        <!-- Form -->
        <form @submit.prevent="submit" class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded">
          <!-- Customer -->
          <div>
            <label>Customer</label>
            <select v-model="form.customer_id" class="border rounded p-2 w-full">
              <option disabled value="">Pilih Customer</option>
              <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </div>

          <!-- Application -->
          <div>
            <label>Application</label>
            <select v-model="form.application_id" class="border rounded p-2 w-full">
              <option disabled value="">Pilih Application</option>
              <option v-for="a in applications" :key="a.id" :value="a.id">{{ a.name }} (v{{ a.version }})</option>
            </select>
          </div>

          <!-- Hosting -->
          <div>
            <label>Hosting</label>
            <select v-model="form.hosting_id" class="border rounded p-2 w-full">
              <option disabled value="">Pilih Hosting</option>
              <option v-for="h in hostings" :key="h.id" :value="h.id">{{ h.nama_hosting }}</option>
            </select>
          </div>

          <!-- Domain -->
          <div>
            <label>Domain</label>
            <InputText v-model="form.domain" placeholder="Domain" class="w-full" />
          </div>

          <!-- Biaya -->
          <div>
            <label>Biaya</label>
            <InputText v-model="form.biaya" type="number" placeholder="Biaya" class="w-full" />
          </div>

          <!-- Tanggal Mulai -->
          <div>
            <label>Tanggal Mulai</label>
            <InputText v-model="form.tanggal_mulai" type="date" class="w-full" />
          </div>

          <!-- Tanggal Expired -->
          <div>
            <label>Tanggal Expired</label>
            <InputText v-model="form.tanggal_expired" type="date" class="w-full" />
          </div>

          <!-- Status -->
          <div>
            <label>Status</label>
            <select v-model="form.status" class="border rounded p-2 w-full">
              <option value="Aktif">Aktif</option>
              <option value="Belum Aktif">Belum Aktif</option>
              <option value="Expired">Expired</option>
            </select>
          </div>

          <!-- Actions -->
          <div class="col-span-2 flex justify-end gap-2 mt-2">
            <Button type="submit" label="Save" :loading="form.processing" :disabled="form.processing" />
            <Button v-if="editingId" label="Cancel" class="p-button-text" @click="() => { form.reset(); editingId = null; }" />
          </div>
        </form>

        <!-- Search -->
        <div class="flex gap-2 justify-end-safe">
          <InputText v-model="search" placeholder="Search..." @keyup.enter="onSearch" />
        </div>

        <!-- Data Table -->
        <DataTable
          :value="datas"
          :lazy="true"
          :loading="loading"
          :paginator="true"
          :rows="10"
          :totalRecords="totalRecords"
          @page="onPage"
          @sort="onSort"
          :rowsPerPageOptions="[5, 10, 25]"
          dataKey="id"
          tableStyle="min-width:70rem"
        >
          <Column field="customer.name" header="Customer" sortable />
          <Column field="application.name" header="Aplikasi" sortable />
          <Column field="hosting.nama_hosting" header="Hosting" sortable />
          <Column field="domain" header="Domain" sortable />
          <Column field="biaya" header="Biaya" sortable />
          <Column field="tanggal_mulai" header="Tgl Mulai" sortable />
          <Column field="tanggal_expired" header="Tgl Expired" sortable />
          <Column field="status" header="Status">
            <template #body="{ data }">
              <span :class="data.status === 'Aktif' ? 'text-green-600' : 'text-red-600'">
                {{ data.status }}
              </span>
            </template>
          </Column>
          <Column header="Actions">
            <template #body="{ data }">
              <Button icon="pi pi-pencil" class="p-button-sm p-button-warning" @click="openEditForm(data)" />
              <Button icon="pi pi-trash" class="p-button-sm p-button-danger ml-2" @click="deleteData(data)" />
            </template>
          </Column>
        </DataTable>
      </div>
    </div>
  </AppLayout>
</template>
