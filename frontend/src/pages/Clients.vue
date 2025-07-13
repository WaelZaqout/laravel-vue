<script setup>
import { ref, onMounted } from 'vue'
import axiosClient from '../axios'
import NotificationSystem, { showSuccess, showError, showWarning } from './NotificationSystem.vue'

// البيانات المرتبطة بالعملاء والنموذج
const users = ref([])
const form = ref({
  name: '',
  title: '',
  email: '',
  phone: '',
  role: 'client'
})

// بيانات الحالة والإجراءات
const showModal = ref(false)
const isEditing = ref(false)
const isSubmitting = ref(false)

// بيانات الصفحات
const currentPage = ref(1)
const totalPages = ref(1)
const perPage = 10

// جلب قائمة العملاء (مع Pagination)
const fetchClients = async () => {
  try {
    const response = await axiosClient.get('/api/clients', {
      params: { page: currentPage.value, per_page: perPage }
    })
    users.value = response.data.data
    totalPages.value = response.data.last_page
  } catch (error) {
    console.error('Error fetching clients:', error)
    showError('حدث خطأ في جلب قائمة العملاء')
  }
}

// حذف عميل
const deleteClient = async (id) => {
  if (confirm('هل أنت متأكد من حذف هذا العميل؟')) {
    try {
      await axiosClient.delete(`/api/clients/${id}`)
      await fetchClients()
      showSuccess('تم حذف العميل بنجاح', 'تم الحذف')
    } catch (error) {
      console.error('Error deleting client:', error)
      showError('حدث خطأ في حذف العميل: ' + (error.response?.data?.message || 'خطأ غير معروف'))
    }
  }
}

// فتح المودال للإضافة
const openAddModal = () => {
  form.value = { name: '', title: '', email: '', phone: '', role: 'client' }
  isEditing.value = false
  showModal.value = true
}

// فتح المودال للتعديل
const editClient = (user) => {
  form.value = { ...user }
  isEditing.value = true
  showModal.value = true
}

// إرسال النموذج (إضافة أو تعديل)
const submitForm = async () => {
  if (isSubmitting.value) return

  // التحقق من البيانات المطلوبة
  if (!form.value.name.trim()) {
    showWarning('يرجى إدخال اسم العميل')
    return
  }
  
  if (!form.value.email.trim()) {
    showWarning('يرجى إدخال البريد الإلكتروني')
    return
  }
  
  // التحقق من صيغة الإيميل
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(form.value.email)) {
    showWarning('يرجى إدخال بريد إلكتروني صحيح')
    return
  }

  isSubmitting.value = true
  
  try {
    if (isEditing.value) {
      await axiosClient.put(`/api/clients/${form.value.id}`, form.value)
      showSuccess('تم تحديث العميل بنجاح', 'تم التحديث')
    } else {
      await axiosClient.post('/api/clients', form.value)
      showSuccess('تم إضافة العميل بنجاح', 'تم الإضافة')
    }

    await fetchClients()
    showModal.value = false
    
    // إعادة تعيين النموذج
    form.value = { name: '', title: '', email: '', phone: '', role: 'client' }
    
  } catch (error) {
    console.error('Error saving client:', error)
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

// التنقل بين الصفحات
const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    fetchClients()
  }
}

// تحميل البيانات عند تحميل الصفحة
onMounted(fetchClients)
</script>

<template>
  
  <div class="bg-gray-100 min-h-screen">
    <!-- Header -->
    <NotificationSystem />
   

    <!-- Main Content -->
    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <div class="bg-white p-6 rounded-lg shadow">
        <!-- Add Button -->
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-semibold text-gray-700">Client List</h2>
          <button @click="openAddModal" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Add Client
          </button>
        </div>

        <!-- Clients Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="user in users" :key="user.email">
                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ user.name }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ user.title }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ user.email }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ user.phone }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ user.role }}</td>
                <td class="px-6 py-4 text-sm text-right">
                  <button @click="editClient(user)" class="text-blue-600 hover:underline mr-2">Edit</button>
                  <button @click.prevent="deleteClient(user.id)" class="text-red-600 hover:text-red-900">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
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
      </div>

      <!-- Modal -->
      <div v-if="showModal" class="fixed inset-0 bg-white/30 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-lg">
          <h2 class="text-xl font-semibold mb-4">{{ isEditing ? 'Edit Client' : 'Add Client' }}</h2>

          <form @submit.prevent="submitForm">
            <input v-model="form.name" type="text" placeholder="Name" class="border p-2 w-full mb-2" required />
            <input v-model="form.title" type="text" placeholder="Title" class="border p-2 w-full mb-2" />
            <input v-model="form.email" type="email" placeholder="Email" class="border p-2 w-full mb-2" required />
            <input v-model="form.phone" type="text" placeholder="Phone" class="border p-2 w-full mb-2" />
            <select v-model="form.role" class="border p-2 w-full mb-4">
              <option value="client">Client</option>
              <option value="admin">Admin</option>
            </select>

            <div class="flex justify-end">
              <button type="button" @click="showModal = false" class="mr-2 px-4 py-2 bg-gray-400 text-white rounded">
                Cancel
              </button>
              <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
                {{ isEditing ? 'Update' : 'Add' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</template>

<style scoped></style>
