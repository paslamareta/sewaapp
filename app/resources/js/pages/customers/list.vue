<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import HeadingSmall from '@/components/HeadingSmall.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import Message from 'primevue/message';
import { useForm } from 'laravel-precognition-vue';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from "primevue/usetoast";
import Divider from 'primevue/divider';
import Toast from 'primevue/toast';

const toast = useToast();
const breadcrumbItems = [{ title: 'Customer', href: '/customers' }];
const confirm = useConfirm();
const datas = ref([]);
const totalRecords = ref(0);
const loading = ref(false);
const search = ref('');
const showForm = ref(false);
const editingId = ref<number | null>(null);
let form = useForm('post', '/api/customers', {
    id: '',
    name: '',
    email: '',
    phone: '',
    address: '',
});
const submit = () => form.submit()
    .then(response => {
        form.reset();
        showForm.value = false;
        loadData(dtParams.value);
        toast.add({ severity: 'success', summary: 'Success', detail: 'Operation completed successfully', life: 3000 });
    })
    .catch(error => {
        toast.add({ severity: 'error', summary: 'Error', detail: 'An error occurred while processing your request', life: 3000 });
    });
const dtParams = ref({
    first: 0,
    rows: 10,
    sortField: 'id',
    sortOrder: 1
});


// --- CRUD Functions ---
function openCreateForm() {
    editingId.value = null;
    form = useForm('post', '/api/customers', {
        id: '',
        name: '',
        email: '',
        phone: '',
        address: '',
    });
    showForm.value = true;
}
function getCookie(name: string) {
    const m = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/([.$?*|{}()\[\]\\/+^])/g, '\\$1') + '=([^;]*)'));
    return m ? decodeURIComponent(m[1]) : '';
}

const deleteData = async (row: any) => {
    const csrfToken = getCookie('XSRF-TOKEN') || getCookie('csrf_token') || '';
    confirm.require({
        message: `Are you sure you want to delete "${row.name}"?`,
        header: 'Confirm Delete',
        group: 'templating',
        rejectProps: {
            icon: 'pi pi-times',
            label: 'Cancel',
            outlined: true
        },
        acceptProps: {
            icon: 'pi pi-check',
            label: 'Confirm'
        },
        accept: () => {
            fetch(`/api/customers/${row.id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': csrfToken, // untuk Laravel
                },
                credentials: 'include' // penting agar cookie terset
            })
                .then(response => {
                    if (!response.ok) throw new Error('Failed to delete');
                    return response.json();
                })
                .then(() => {
                    loadData(dtParams.value);
                    toast.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: 'Application deleted successfully',
                        life: 3000
                    });
                })
                .catch(() => {
                    toast.add({
                        severity: 'error',
                        summary: 'Error',
                        detail: 'An error occurred while deleting the customer',
                        life: 3000
                    });
                });

        },
        reject: () => {
            toast.add({ severity: 'error', summary: 'Rejected', detail: 'You have rejected', life: 3000 });
        }
    });
};

async function openEditForm(row: any) {
    editingId.value = row.id;
    form = useForm('put', `/api/customers/${row.id}`, {
        id: row.id,
        name: row.name,
        email: row.email,
        phone: row.phone,
        address: row.address,
    });
    showForm.value = true;
}


// --- Load Data with pagination, sorting, and search ---
async function loadData(params: any) {
    loading.value = true;
    try {
        dtParams.value = { ...dtParams.value, ...params };
        const sortField = dtParams.value.sortField || 'id';
        const sortOrder = dtParams.value.sortOrder > 0 ? 'asc' : 'desc';
        const res = await fetch(`/api/customers?skip=${dtParams.value.first}&take=${dtParams.value.rows}&sortField=${sortField}&sortOrder=${sortOrder}&search=${encodeURIComponent(search.value)}`);
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

// Wrapper PrimeVue events
function onPage(event: any) {
    loadData({
        first: event.first,
        rows: event.rows,
        sortField: event.sortField || dtParams.value.sortField,
        sortOrder: event.sortOrder || dtParams.value.sortOrder
    });
}

function onSort(event: any) {
    loadData({
        sortField: event.sortField,
        sortOrder: event.sortOrder
    });
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

        <Head title="Customers" />

        <div class="flex h-full flex-col gap-4 p-4 overflow-x-auto">
            <div class="space-y-4">
                <!-- Header -->
                <div class="between flex items-center justify-between">
                    <HeadingSmall title="Customers" description="Manage your Customers" />
                    <Button label="Add Customer" icon="pi pi-plus" @click="openCreateForm" />
                </div>
                <Divider />

                <!-- Search -->
                <div class="flex gap-2 justify-end-safe">
                    <InputText v-model="search" placeholder="Search..." @keyup.enter="onSearch" />
                </div>

                <!-- Data Table -->
                <DataTable :value="datas" :lazy="true" :loading="loading" :paginator="true" :rows="10"
                    :totalRecords="totalRecords" @page="onPage" @sort="onSort" :rowsPerPageOptions="[5, 10, 25]"
                    dataKey="id" tableStyle="min-width:50rem">
                    <Column field="name" header="Name" sortable />
                    <Column field="email" header="Email" sortable />
                    <Column field="phone" header="Phone" sortable />
                    <Column field="address" header="Address" sortable />
                    <Column header="Actions">
                        <template #body="{ data }">
                            <Button icon="pi pi-pencil" class="p-button-sm p-button-warning"
                                @click="openEditForm(data)" />
                            <Button icon="pi pi-trash" class="p-button-sm p-button-danger ml-2"
                                @click="deleteData(data)" />
                        </template>
                    </Column>
                </DataTable>

                <!-- Form Dialog -->
                <Dialog v-model:visible="showForm" modal :header="form.id ? 'Edit Customer' : 'Add Customer'"
                    :style="{ width: '50rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
                    <form @submit.prevent="submit">
                        <div class="flex flex-col gap-3">
                            <div class="flex flex-col gap-1">
                                <InputText v-model="form.name" placeholder="Name" @change="form.validate('name')" />
                                <Message v-if="form.invalid('name')" severity="error" variant="simple" size="small"
                                    :closable="false">
                                    {{ form.errors.name }}
                                </Message>
                            </div>

                            <div class="flex flex-col gap-1">
                                <InputText v-model="form.email" placeholder="Email" @change="form.validate('email')" />
                                <Message v-if="form.invalid('email')" severity="error" variant="simple" size="small"
                                    :closable="false">
                                    {{ form.errors.email }}
                                </Message>
                            </div>

                            <div class="flex flex-col gap-1">
                                <InputText v-model="form.phone" placeholder="Phone" @change=" form.validate('phone')" />
                                <Message v-if="form.invalid('phone')" severity="error" variant="simple" size="small"
                                    :closable="false">
                                    {{ form.errors.phone }}
                                </Message>
                            </div>

                            <div class="flex flex-col gap-1">
                                <InputText v-model="form.address" placeholder="Address"
                                    @change="form.validate('address')" />
                                <Message v-if="form.invalid('address')" severity="error" variant="simple" size="small"
                                    :closable="false">
                                    {{ form.errors.address }}
                                </Message>
                            </div>

                            <div class="flex justify-end gap-2">
                                <Button label="Cancel" @click="showForm = false" class="p-button-text" />
                                <Button type="submit" label="Save" :loading="form.processing"
                                    :disabled="form.processing" />
                            </div>
                        </div>
                    </form>

                </Dialog>
            </div>
        </div>
    </AppLayout>
</template>
