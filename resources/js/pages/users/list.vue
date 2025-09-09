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
import Dialog from 'primevue/dialog';
import { useForm } from 'laravel-precognition-vue';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';

const toast = useToast();
const confirm = useConfirm();

const breadcrumbItems = [{ title: 'Users', href: '/users' }];

const datas = ref<any[]>([]);
const totalRecords = ref(0);
const loading = ref(false);
const search = ref('');
const editingId = ref<number | null>(null);

const showDialog = ref(false);

let form = useForm('post', '/users', {
  name: '',
  email: '',
  password: ''
});

const openAddForm = () => {
  editingId.value = null;
  form = useForm('post', '/users', {
    name: '',
    email: '',
    password: ''
  });
  showDialog.value = true;
};

async function openEditForm(row: any) {
  editingId.value = row.id;
  form = useForm('put', `/users/${row.id}`, {
    name: row.name,
    email: row.email,
    password: ''
  });
  showDialog.value = true;
}

const submit = () =>
  form.submit()
    .then(() => {
      form.reset();
      editingId.value = null;
      showDialog.value = false;
      loadData(dtParams.value);
      toast.add({ severity: 'success', summary: 'Success', detail: 'Operation completed successfully', life: 3000 });
    })
    .catch(() => {
      toast.add({ severity: 'error', summary: 'Error', detail: 'An error occurred while processing your request', life: 3000 });
    });

// --- CRUD ---
function getCookie(name: string) {
  const m = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/([.$?*|{}()\[\]\\/+^])/g, '\\$1') + '=([^;]*)'));
  return m ? decodeURIComponent(m[1]) : '';
}

const deleteData = async (row: any) => {
  const csrfToken = getCookie('XSRF-TOKEN') || getCookie('csrf_token') || '';
  confirm.require({
    message: 'Yakin mau hapus user ini?',
    header: 'Konfirmasi Hapus',
    group: 'templating',
    rejectProps: { icon: 'pi pi-times', label: 'Batal', outlined: true },
    acceptProps: { icon: 'pi pi-check', label: 'Hapus' },
    accept: () => {
      fetch(`/users/${row.id}`, {
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
          toast.add({ severity: 'success', summary: 'Success', detail: 'User berhasil dihapus', life: 3000 });
        })
        .catch(() => {
          toast.add({ severity: 'error', summary: 'Error', detail: 'Gagal menghapus user', life: 3000 });
        });
    }
  });
};

// --- Load Data ---
const dtParams = ref({ first: 0, rows: 10, sortField: 'name', sortOrder: 1 });

async function loadData(params: any) {
  loading.value = true;
  try {
    dtParams.value = { ...dtParams.value, ...params };
    const sortField = dtParams.value.sortField || 'name';
    const sortOrder = dtParams.value.sortOrder > 0 ? 'asc' : 'desc';
    const res = await fetch(`/api/users?skip=${dtParams.value.first}&take=${dtParams.value.rows}&sortField=${sortField}&sortOrder=${sortOrder}&search=${encodeURIComponent(search.value)}`);
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

onMounted(() => {
  loadData(dtParams.value);
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
    <Head title="Users" />
    <div class="flex h-full flex-col gap-4 p-4 overflow-x-auto">
      <div class="space-y-4">
        <!-- Header -->
        <div class="flex justify-between items-center">
          <HeadingSmall title="Users" description="Kelola pengguna aplikasi" />
          <Button label="Add User" icon="pi pi-plus" @click="openAddForm" />
        </div>
        <Divider />

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
          tableStyle="min-width:50rem"
        >
          <Column field="name" header="Nama" sortable />
          <Column field="email" header="Email" sortable />
          <Column header="Actions">
            <template #body="{ data }">
              <Button icon="pi pi-pencil" class="p-button-sm p-button-warning" @click="openEditForm(data)" />
              <Button icon="pi pi-trash" class="p-button-sm p-button-danger ml-2" @click="deleteData(data)" />
            </template>
          </Column>
        </DataTable>
      </div>
    </div>

    <!-- Dialog Form -->
    <Dialog v-model:visible="showDialog" :header="editingId ? 'Edit User' : 'Add User'" modal>
      <form @submit.prevent="submit" class="flex flex-col gap-3">
        <InputText v-model="form.name" placeholder="Nama" class="w-full" />
        <InputText v-model="form.email" placeholder="Email" class="w-full" />
        <InputText v-model="form.password" type="password" placeholder="Password" class="w-full" />

        <div class="flex justify-end gap-2 mt-2">
          <Button type="button" label="Cancel" class="p-button-text" @click="showDialog = false" />
          <Button type="submit" label="Save" :loading="form.processing" :disabled="form.processing" />
        </div>
      </form>
    </Dialog>
  </AppLayout>
</template>
