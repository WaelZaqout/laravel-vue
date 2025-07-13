<script setup>
import { ref } from 'vue'

const user = ref({
  name: 'Wael Zaqout',
  email: 'wael@example.com',
  joined: 'March 2024',
  avatar: 'https://i.pravatar.cc/150?img=10',
  balance: 1234.56,
  stats: {
    paid: 24,
    unpaid: 3,
  },
  note: 'This user is a trusted customer. Always pays on time.',
})

const editProfile = () => {
  alert('Profile edit clicked')
}

const changePassword = () => {
  alert('Change password clicked')
}
</script>

<template>
  <div>
    <header class="bg-white shadow-sm">
      <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Profile</h1>
      </div>
    </header>
    <main>
      <div class="min-h-screen bg-gray-100 p-6">
        <div class="min-h-screen bg-gray-100 py-10 px-4">
          <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white p-6">
              <div class="flex items-center space-x-4">
                <img :src="user.avatar" alt="avatar" class="w-20 h-20 rounded-full border-4 border-white shadow" />
                <div>
                  <h2 class="text-2xl font-bold">{{ user.name }}</h2>
                  <p class="text-sm text-gray-200">{{ user.email }}</p>
                  <p class="text-sm text-gray-300 mt-1">Joined: {{ user.joined }}</p>
                </div>
              </div>
            </div>

            <!-- Content -->
            <div class="p-6">
              <!-- Stats -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-gray-100 p-4 rounded-lg text-center">
                  <p class="text-sm text-gray-500">Total Balance</p>
                  <p class="text-2xl font-semibold text-blue-600">${{ user.balance.toFixed(2) }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg text-center">
                  <p class="text-sm text-gray-500">Paid Invoices</p>
                  <p class="text-2xl font-semibold text-green-600">{{ user.stats.paid }}</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg text-center">
                  <p class="text-sm text-gray-500">Unpaid Invoices</p>
                  <p class="text-2xl font-semibold text-red-500">{{ user.stats.unpaid }}</p>
                </div>
              </div>

              <!-- Bio or Note -->
              <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2">Notes</h3>
                <p class="text-gray-700">
                  {{ user.note }}
                </p>
              </div>

              <!-- Actions -->
              <div class="flex space-x-4">
                <button @click="editProfile" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                  Edit Profile
                </button>
                <button @click="changePassword" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                 Delete Client
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<style scoped>
/* Optional: Smooth tab switching */
button {
  transition: all 0.2s ease-in-out;
}
</style>
