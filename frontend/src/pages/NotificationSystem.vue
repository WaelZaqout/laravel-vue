<template>
  <div class="notification-container">
    <transition-group name="notification" tag="div">
      <div
        v-for="notification in notifications"
        :key="notification.id"
        :class="[
          'notification',
          `notification-${notification.type}`
        ]"
        @click="removeNotification(notification.id)"
      >
        <div class="notification-content">
          <div class="notification-icon">
            <svg v-if="notification.type === 'success'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <svg v-else-if="notification.type === 'error'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <svg v-else-if="notification.type === 'warning'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
          </div>
          <div class="notification-text">
            <div class="notification-title">{{ notification.title }}</div>
            <div class="notification-message">{{ notification.message }}</div>
          </div>
          <button class="notification-close" @click.stop="removeNotification(notification.id)">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
          </button>
        </div>
      </div>
    </transition-group>
  </div>
</template>

<script>
import { ref, nextTick } from 'vue'

// Store للإشعارات
const notifications = ref([])
let notificationId = 0

// دالة لإضافة إشعار جديد
const addNotification = (notification) => {
  const id = ++notificationId
  const newNotification = {
    id,
    type: notification.type || 'info',
    title: notification.title || 'إشعار',
    message: notification.message || '',
    duration: notification.duration || 4000
  }
  
  notifications.value.push(newNotification)
  
  // إزالة الإشعار تلقائياً بعد المدة المحددة
  if (newNotification.duration > 0) {
    setTimeout(() => {
      removeNotification(id)
    }, newNotification.duration)
  }
}

// دالة لإزالة إشعار
const removeNotification = (id) => {
  const index = notifications.value.findIndex(n => n.id === id)
  if (index > -1) {
    notifications.value.splice(index, 1)
  }
}

// دوال مساعدة للأنواع المختلفة
const showSuccess = (message, title = 'نجح العملية') => {
  addNotification({ type: 'success', title, message })
}

const showError = (message, title = 'خطأ') => {
  addNotification({ type: 'error', title, message })
}

const showWarning = (message, title = 'تحذير') => {
  addNotification({ type: 'warning', title, message })
}

const showInfo = (message, title = 'معلومة') => {
  addNotification({ type: 'info', title, message })
}

export default {
  name: 'NotificationSystem',
  setup() {
    return {
      notifications,
      removeNotification
    }
  }
}

// تصدير الدوال للاستخدام في مكونات أخرى
export {
  showSuccess,
  showError,
  showWarning,
  showInfo,
  addNotification
}
</script>

<style scoped>
.notification-container {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 1000;
  max-width: 400px;
  pointer-events: none;
}

.notification {
  background: white;
  border-radius: 8px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  margin-bottom: 12px;
  overflow: hidden;
  pointer-events: auto;
  cursor: pointer;
  transition: all 0.3s ease;
  border-left: 4px solid;
}

.notification:hover {
  transform: translateX(-5px);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
}

.notification-success {
  border-left-color: #10b981;
}

.notification-error {
  border-left-color: #ef4444;
}

.notification-warning {
  border-left-color: #f59e0b;
}

.notification-info {
  border-left-color: #3b82f6;
}

.notification-content {
  display: flex;
  align-items: flex-start;
  padding: 16px;
  gap: 12px;
}

.notification-icon {
  flex-shrink: 0;
  width: 24px;
  height: 24px;
  margin-top: 2px;
}

.notification-success .notification-icon {
  color: #10b981;
}

.notification-error .notification-icon {
  color: #ef4444;
}

.notification-warning .notification-icon {
  color: #f59e0b;
}

.notification-info .notification-icon {
  color: #3b82f6;
}

.notification-text {
  flex: 1;
  min-width: 0;
}

.notification-title {
  font-weight: 600;
  font-size: 14px;
  color: #1f2937;
  margin-bottom: 4px;
}

.notification-message {
  font-size: 13px;
  color: #6b7280;
  line-height: 1.4;
}

.notification-close {
  flex-shrink: 0;
  width: 20px;
  height: 20px;
  color: #9ca3af;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.2s ease;
}

.notification-close:hover {
  color: #6b7280;
}

/* Animation */
.notification-enter-active {
  transition: all 0.4s ease;
}

.notification-leave-active {
  transition: all 0.3s ease;
}

.notification-enter-from {
  transform: translateX(100%);
  opacity: 0;
}

.notification-leave-to {
  transform: translateX(100%);
  opacity: 0;
}

.notification-move {
  transition: transform 0.3s ease;
}
</style>