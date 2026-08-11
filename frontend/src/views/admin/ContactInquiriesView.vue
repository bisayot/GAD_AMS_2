<template>
  <div class="px-6 py-8 mx-auto max-w-7xl">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-headline font-bold text-slate-800">Contact Inquiries</h1>
        <p class="text-slate-500 mt-1">Review and manage inquiries submitted from the public contact form.</p>
      </div>
      <button @click="fetchInquiries" class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 text-slate-600 font-medium transition-colors">
        <span class="material-symbols-outlined text-[20px]">refresh</span>
        Refresh
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
          <span class="material-symbols-outlined">inbox</span>
        </div>
        <div>
          <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Total Inquiries</p>
          <p class="text-2xl font-bold text-slate-800">{{ inquiries.length }}</p>
        </div>
      </div>
      
      <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center">
          <span class="material-symbols-outlined">mark_email_unread</span>
        </div>
        <div>
          <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Unread</p>
          <p class="text-2xl font-bold text-slate-800">{{ unreadCount }}</p>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
      <!-- Loading State -->
      <div v-if="loading" class="p-12 flex flex-col items-center justify-center text-slate-400">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-4"></div>
        <p>Loading inquiries...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="inquiries.length === 0" class="p-12 flex flex-col items-center justify-center text-slate-400">
        <span class="material-symbols-outlined text-5xl mb-4 text-slate-300">mail</span>
        <h3 class="text-lg font-medium text-slate-700 mb-1">No Inquiries Found</h3>
        <p>There are currently no contact form submissions.</p>
      </div>

      <!-- Inquiries List -->
      <div v-else class="divide-y divide-slate-100">
        <div 
          v-for="inquiry in inquiries" 
          :key="inquiry.id"
          class="p-6 transition-colors duration-200 hover:bg-slate-50 flex flex-col gap-4 group"
          :class="{ 'bg-blue-50/30': inquiry.status === 'new' }"
        >
          <!-- Header: Status, Date, Name -->
          <div class="flex justify-between items-start">
            <div class="flex items-center gap-3">
              <span v-if="inquiry.status === 'new'" class="w-2.5 h-2.5 rounded-full bg-blue-500 mt-1"></span>
              <span v-else-if="inquiry.status === 'replied_staff'" class="w-2.5 h-2.5 rounded-full bg-green-500 mt-1" title="Replied by Staff"></span>
              <span v-else-if="inquiry.status === 'replied_director'" class="w-2.5 h-2.5 rounded-full bg-purple-500 mt-1" title="Replied by Director"></span>
              <div>
                <h4 class="text-lg font-semibold text-slate-800" :class="{'font-bold': inquiry.status === 'new'}">
                  {{ inquiry.name }}
                </h4>
                <a :href="'mailto:' + inquiry.email" class="text-sm text-primary hover:underline flex items-center gap-1 mt-0.5">
                  <span class="material-symbols-outlined text-[14px]">mail</span>
                  {{ inquiry.email }}
                </a>
              </div>
            </div>
            
            <div class="flex items-center gap-4">
              <span class="text-sm text-slate-500 bg-slate-100 px-3 py-1 rounded-full whitespace-nowrap">
                {{ formatDate(inquiry.created_at) }}
              </span>
              
              <div v-if="inquiry.status === 'replied_staff'" class="text-xs font-bold px-2 py-1 bg-green-100 text-green-700 rounded-full border border-green-200">
                Replied by Staff
              </div>
              <div v-if="inquiry.status === 'replied_director'" class="text-xs font-bold px-2 py-1 bg-purple-100 text-purple-700 rounded-full border border-purple-200">
                Replied by Director
              </div>

              <button 
                v-if="!inquiry.status.startsWith('replied')"
                @click="openReplyModal(inquiry)"
                class="text-sm px-3 py-1 bg-primary text-white border border-transparent rounded hover:bg-primary-dark transition-colors flex items-center gap-1"
                title="Reply"
              >
                <span class="material-symbols-outlined text-[16px]">reply</span> Reply
              </button>
              
              <button 
                v-if="inquiry.status === 'new'"
                @click="markAsRead(inquiry)"
                class="text-sm px-3 py-1 bg-white border border-slate-300 rounded text-slate-600 hover:bg-slate-50 hover:text-slate-800 transition-colors opacity-0 group-hover:opacity-100 focus:opacity-100 flex items-center gap-1"
                title="Mark as Read"
              >
                <span class="material-symbols-outlined text-[16px]">done</span> Mark Read
              </button>
            </div>
          </div>

          <!-- Content: Subject & Message -->
          <div class="ml-0 lg:ml-6 mt-2">
            <h5 class="font-medium text-slate-700 mb-2">Subject: {{ inquiry.subject }}</h5>
            <div class="bg-slate-50 rounded-lg p-4 border border-slate-100 text-slate-600 text-sm whitespace-pre-wrap leading-relaxed">
              {{ inquiry.message }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Reply Modal -->
    <div v-if="showReplyModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg flex flex-col">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
          <h3 class="text-xl font-bold text-slate-800">Reply to {{ activeInquiry?.name }}</h3>
          <button @click="closeReplyModal" class="text-slate-400 hover:text-slate-600">
            <span class="material-symbols-outlined">close</span>
          </button>
        </div>
        
        <div class="p-6 flex-grow overflow-y-auto">
          <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800 flex items-start gap-2">
            <span class="material-symbols-outlined text-amber-600 text-[20px]">warning</span>
            <p>
              <strong>Note:</strong> Our system uses a free email service with daily limits. If sending fails, please close this and reply manually using your own email client.
            </p>
          </div>
          
          <label class="block text-sm font-semibold text-slate-700 mb-2">Message</label>
          <textarea 
            v-model="replyMessage" 
            rows="6" 
            class="w-full border-slate-200 rounded-lg focus:ring-primary focus:border-primary p-3" 
            placeholder="Type your reply here..."
          ></textarea>
        </div>
        
        <div class="p-6 border-t border-slate-100 bg-slate-50 flex justify-end gap-3 rounded-b-xl">
          <button @click="closeReplyModal" class="px-4 py-2 text-slate-600 hover:bg-slate-200 bg-slate-100 rounded-lg font-medium transition-colors">
            Cancel
          </button>
          <button @click="submitReply" :disabled="replying || !replyMessage.trim()" class="px-6 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark disabled:opacity-50 transition-colors flex items-center gap-2">
            <span v-if="replying" class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
            {{ replying ? 'Sending...' : 'Send Reply' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '../../api';
import Swal from 'sweetalert2';

const inquiries = ref([]);
const loading = ref(true);

const showReplyModal = ref(false);
const activeInquiry = ref(null);
const replyMessage = ref('');
const replying = ref(false);

const unreadCount = computed(() => {
  return inquiries.value.filter(i => i.status === 'new').length;
});

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const fetchInquiries = async () => {
  loading.value = true;
  try {
    const res = await api.get('/contact-inquiries');
    if (res.data && res.data.inquiries) {
      inquiries.value = res.data.inquiries;
    }
  } catch (error) {
    console.error('Error fetching inquiries:', error);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Failed to load inquiries.'
    });
  } finally {
    loading.value = false;
  }
};

const markAsRead = async (inquiry) => {
  try {
    const res = await api.post(`/contact-inquiries/${inquiry.id}/read`);
    if (res.status === 200 || res.data.message) {
      inquiry.status = 'read';
    }
  } catch (error) {
    console.error('Error marking as read:', error);
  }
};

const openReplyModal = (inquiry) => {
  activeInquiry.value = inquiry;
  replyMessage.value = '';
  showReplyModal.value = true;
};

const closeReplyModal = () => {
  showReplyModal.value = false;
  activeInquiry.value = null;
  replyMessage.value = '';
};

const submitReply = async () => {
  if (!activeInquiry.value || !replyMessage.value.trim()) return;
  
  replying.value = true;
  try {
    const res = await api.post(`/contact-inquiries/${activeInquiry.value.id}/reply`, {
      reply_message: replyMessage.value
    });
    
    if (res.status === 200 || res.data.message) {
      Swal.fire({
        icon: 'success',
        title: 'Reply Sent',
        text: 'Your reply was successfully emailed.',
        timer: 2000,
        showConfirmButton: false
      });
      activeInquiry.value.status = res.data.status || 'replied_staff';
      closeReplyModal();
    }
  } catch (error) {
    console.error('Error sending reply:', error);
    Swal.fire({
      icon: 'error',
      title: 'Failed to Send',
      text: error?.messages?.error || error?.message || 'The email could not be sent. You might have reached your daily limit. Please try manually.'
    });
  } finally {
    replying.value = false;
  }
};

onMounted(() => {
  fetchInquiries();
});
</script>

<style scoped>
</style>
