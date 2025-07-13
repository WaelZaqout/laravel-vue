<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <div class="w-64 bg-indigo-700 text-white flex flex-col justify-between">
      <div class="px-4 py-6">
        <!-- App Logo -->
        <div class="p-6 text-2xl font-bold">MyApp</div>

        <!-- Navigation Links -->
        <nav class="space-y-2">
          <router-link
            v-for="item in navigation"
            :key="item.name"
            :to="item.to"
            class="flex items-center px-3 py-2 rounded-lg hover:bg-indigo-600 transition-colors"
            :class="{ 'bg-indigo-800': $route.path === item.to }"
          >
            <component :is="item.icon" class="w-5 h-5 mr-3" />
            <span>{{ item.name }}</span>
          </router-link>
        </nav>
      </div>

      <!-- Profile Dropdown -->
      <div class="border-t border-gray-700 pb-3 pt-4">
        <div class="flex items-center px-5">
          <div class="shrink-0">
            <img
              class="size-10 rounded-full"
              :src="userStore.user?.avatar || 'https://randomuser.me/api/portraits/men/81.jpg'"
              alt="User Avatar"
            />
          </div>
          <div class="ml-3">
            <div class="text-base font-medium text-white">
              {{ userName }}
            </div>
            <div class="text-sm font-medium text-gray-400">
              {{ userStore.user?.email || 'email@example.com' }}
            </div>
          </div>
        </div>

        <!-- Logout Button -->
        <div class="mt-3 space-y-1 px-2">
          <button
            @click="logout"
            :disabled="isLoggingOut"
            class="block w-full text-left rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ isLoggingOut ? 'Logging out...' : 'Sign out' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6">
      <router-view />
    </div>

    <!-- Loading Overlay -->
    <div v-if="isLoggingOut" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="flex items-center space-x-3">
          <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600"></div>
          <span class="text-gray-700">Logging out...</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useUserStore } from '../store/user'
import axiosClient from '../axios'
import { computed, ref } from 'vue'

// Pinia store
const userStore = useUserStore()
const router = useRouter()
const isLoggingOut = ref(false)

const userName = computed(() => userStore.userName)

// Improved logout handler
async function logout() {
  if (isLoggingOut.value) return
  
  isLoggingOut.value = true
  
  try {
    // Try to logout from server
    await axiosClient.post('/api/logout')
    console.log('Successfully logged out from server')
  } catch (error) {
    console.error('Failed to logout from server:', error.response?.data)
    
    // If error is 401, session is already expired
    if (error.response?.status === 401) {
      console.log('Session already expired, logging out locally')
    } else {
      // For other errors, we can show a message to user
      console.warn('Error occurred during logout, logging out locally')
    }
  } finally {
    // Always logout locally regardless of server response
    try {
      userStore.logout() // This will clear user and token
      console.log('Successfully logged out locally')
    } catch (storeError) {
      console.error('Error logging out from store:', storeError)
    }
    
    // Redirect to login page
    router.push({ name: 'Login' })
    
    isLoggingOut.value = false
  }
}

// Navigation items
const navigation = [
  { name: 'Home', to: '/' },
  { name: 'Client', to: '/client' },
  { name: 'Invoice', to: '/invoice' },
  { name: 'Profile', to: '/profile' },
]
</script>