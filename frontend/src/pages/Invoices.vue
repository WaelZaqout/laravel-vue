<script setup>
import { ref, onMounted, nextTick } from 'vue'
import axiosClient from '../axios'
import NotificationSystem, { showSuccess, showError, showWarning } from './NotificationSystem.vue'

const invoices = ref([])
const clients = ref([])
const currentPage = ref(1)
const totalPages = ref(1)
const perPage = 8

const form = ref({
  id: '',
  client_id: '', // تغيير من client إلى client_id
  amount: '',
  status: ''
})

const showModal = ref(false)
const isEditing = ref(false)
const isSubmitting = ref(false)

const fetchInvoices = async () => {
  try {
    const response = await axiosClient.get('/api/invoices', {
      params: { page: currentPage.value, per_page: perPage }
    })
    invoices.value = response.data.data
    totalPages.value = response.data.last_page
  } catch (error) {
    console.error('Error fetching invoices:', error)
    showError('حدث خطأ في جلب الفواتير')
  }
}

// جلب العملاء - تصحيح للتعامل مع pagination
const fetchClientsList = async () => {
  try {
    const response = await axiosClient.get('/api/clients')
    // التحقق من وجود pagination
    if (response.data.data) {
      clients.value = response.data.data
    } else {
      clients.value = response.data
    }
  } catch (error) {
    console.error('Error fetching clients:', error)
    showError('حدث خطأ في جلب قائمة العملاء')
  }
}

// دالة للحصول على اسم العميل بناءً على ID
const getClientName = (clientId) => {
  const client = clients.value.find(c => c.id == clientId)
  return client ? client.name : 'Unknown Client'
}

const deleteInvoice = async (id) => {
  if (confirm('هل أنت متأكد من حذف هذه الفاتورة؟')) {
    try {
      await axiosClient.delete(`/api/invoices/${id}`)
      await fetchInvoices()
      showSuccess('تم حذف الفاتورة بنجاح', 'تم الحذف')
    } catch (error) {
      console.error('Error deleting invoice:', error)
      showError('حدث خطأ في حذف الفاتورة: ' + (error.response?.data?.message || 'خطأ غير معروف'))
    }
  }
}

const submitForm = async () => {
  if (isSubmitting.value) return
  
  // التحقق من البيانات المطلوبة
  if (!form.value.client_id) {
    showWarning('يرجى اختيار العميل')
    return
  }
  
  if (!form.value.amount || form.value.amount <= 0) {
    showWarning('يرجى إدخال مبلغ صحيح')
    return
  }
  
  if (!form.value.status) {
    showWarning('يرجى اختيار حالة الفاتورة')
    return
  }
  
  isSubmitting.value = true
  await nextTick()

  try {
    // إعداد البيانات المرسلة
    const formData = {
      client_id: form.value.client_id,
      amount: form.value.amount,
      status: form.value.status
    }
    
    // إضافة id في حالة التعديل
    if (isEditing.value) {
      await axiosClient.put(`/api/invoices/${form.value.id}`, formData)
      showSuccess('تم تحديث الفاتورة بنجاح', 'تم التحديث')
    } else {
      await axiosClient.post('/api/invoices', formData)
      showSuccess('تم إضافة الفاتورة بنجاح', 'تم الإضافة')
    }
    
    await fetchInvoices()
    showModal.value = false
    
    // إعادة تعيين النموذج
    form.value = {
      id: '',
      client_id: '',
      amount: '',
      status: ''
    }
    
  } catch (error) {
    console.error('Error saving invoice:', error)
    if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat()
      showError('أخطاء في التحقق: ' + errors.join(', '))
    } else {
      showError('حدث خطأ: ' + (error.response?.data?.message || 'خطأ غير معروف'))
    }
  } finally {
    isSubmitting.value = false
  }
}

const editInvoice = (invoice) => {
  form.value = {
    id: invoice.id,
    client_id: invoice.client_id, // تصحيح هنا
    amount: invoice.amount,
    status: invoice.status
  }
  isEditing.value = true
  showModal.value = true
}

const openAddModal = () => {
  form.value = {
    id: '',
    client_id: '', // تصحيح هنا
    amount: '',
    status: ''
  }
  isEditing.value = false
  showModal.value = true
}

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    fetchInvoices()
  }
}

onMounted(() => {
  fetchInvoices()
  fetchClientsList()
})
</script>

<template>
  <div class="bg-gray-50 min-h-screen p-6">
    <!-- نظام الإشعارات -->
    <NotificationSystem />
    
    <div class="max-w-7xl mx-auto">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Invoices</h1>
        <button @click="openAddModal" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded shadow">
          Add Invoice
        </button>
      </div>

      <div class="overflow-y-auto max-h-[500px] bg-white shadow rounded-lg">
        <table class="w-full text-sm text-left">
          <thead class="bg-gray-100 text-xs text-gray-600 uppercase">
            <tr>
              <th class="px-6 py-3">ID</th>
              <th class="px-6 py-3">Client</th>
              <th class="px-6 py-3">Amount</th>
              <th class="px-6 py-3">Status</th>
              <th class="px-6 py-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="invoice in invoices" :key="invoice.id" class="border-b hover:bg-gray-50">
              <td class="px-6 py-4">{{ invoice.id }}</td>
              <td class="px-6 py-4">
                {{ getClientName(invoice.client_id) }}
              </td>
              <td class="px-6 py-4">{{ Number(invoice.amount).toFixed(2) }}</td>
              <td class="px-6 py-4">
                <span :class="[
                  'px-2 py-1 rounded text-xs font-semibold',
                  invoice.status.toLowerCase() === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                    invoice.status.toLowerCase() === 'active' ? 'bg-green-100 text-green-800' :
                      invoice.status.toLowerCase() === 'paid' ? 'bg-blue-100 text-blue-800' :
                        'bg-red-100 text-red-800'
                ]">
                  {{ invoice.status }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex gap-3">
                  <button @click="editInvoice(invoice)" class="text-blue-600 hover:underline">Edit</button>
                  <button @click="deleteInvoice(invoice.id)" class="text-red-600 hover:underline">Delete</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex justify-center mt-6">
        <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
          class="px-3 py-1 mx-1 border rounded bg-gray-100 hover:bg-gray-200 disabled:opacity-50">Prev</button>

        <button v-for="page in totalPages" :key="page" @click="goToPage(page)"
          :class="['px-3 py-1 mx-1 border rounded', currentPage === page ? 'bg-blue-600 text-white' : 'bg-gray-100 hover:bg-gray-200']">
          {{ page }}
        </button>

        <button @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages"
          class="px-3 py-1 mx-1 border rounded bg-gray-100 hover:bg-gray-200 disabled:opacity-50">Next</button>
      </div>

      <!-- Modal -->
      <div v-if="showModal" class="fixed inset-0 bg-white/30 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-lg">
          <h2 class="text-xl font-semibold mb-4">{{ isEditing ? 'Edit Invoice' : 'Add Invoice' }}</h2>

          <form @submit.prevent="submitForm">
            <input v-model="form.id" type="text" placeholder="ID" class="border p-2 w-full mb-2" required />
            <select v-model="form.client_id" class="border p-2 w-full mb-2" required>
              <option disabled value="">Select Client</option>
              <option v-for="client in clients" :key="client.id" :value="client.id">
                {{ client.name }}
              </option>
            </select>
            <input v-model="form.amount" type="number" placeholder="Amount" class="border p-2 w-full mb-2" required />
            <select v-model="form.status" class="border p-2 w-full mb-2" required>
              <option disabled value="">Select Status</option>
              <option value="pending">Pending</option>
              <option value="active">Active</option>
              <option value="paid">Paid</option>
            </select>

            <div class="flex justify-end">
              <button type="button" @click="showModal = false"
                class="mr-2 px-4 py-2 bg-gray-400 text-white rounded">Cancel</button>
              <button type="submit" :disabled="isSubmitting"
                class="px-4 py-2 bg-blue-600 text-white rounded disabled:opacity-50">
                {{ isEditing ? 'Update' : 'Add' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
body {
  font-family: 'Inter', sans-serif;
}
</style>