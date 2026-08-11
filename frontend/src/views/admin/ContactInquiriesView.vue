<template>
  <div class="px-6 py-8 mx-auto max-w-7xl">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-headline font-bold text-on-surface">Contact Inquiries</h1>
        <p class="text-on-surface-variant mt-1">Review and manage inquiries submitted from the public contact form.</p>
      </div>
      <button @click="fetchInquiries" class="flex items-center gap-2 px-4 py-2 bg-surface-container-low border border-outline-variant/50 rounded-lg shadow-sm hover:bg-surface-container text-on-surface-variant font-medium transition-colors">
        <span class="material-symbols-outlined text-[20px]">refresh</span>
        Refresh
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-surface-container-lowest rounded-xl p-6 shadow-sm border border-outline-variant/15 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center">
          <span class="material-symbols-outlined">inbox</span>
        </div>
        <div>
          <p class="text-sm font-semibold text-on-surface-variant uppercase tracking-wider">Total Inquiries</p>
          <p class="text-2xl font-bold text-on-surface">{{ inquiries.length }}</p>
        </div>
      </div>
      
      <div class="bg-surface-container-lowest rounded-xl p-6 shadow-sm border border-outline-variant/15 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-secondary/10 text-secondary flex items-center justify-center">
          <span class="material-symbols-outlined">mark_email_unread</span>
        </div>
        <div>
          <p class="text-sm font-semibold text-on-surface-variant uppercase tracking-wider">Unread</p>
          <p class="text-2xl font-bold text-on-surface">{{ unreadCount }}</p>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/15 overflow-hidden">
      <!-- Loading State -->
      <div v-if="loading" class="p-12 flex flex-col items-center justify-center text-on-surface-variant">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-4"></div>
        <p>Loading inquiries...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="inquiries.length === 0" class="p-12 flex flex-col items-center justify-center text-on-surface-variant">
        <span class="material-symbols-outlined text-5xl mb-4 opacity-50">mail</span>
        <h3 class="text-lg font-medium text-on-surface mb-1">No Inquiries Found</h3>
        <p>There are currently no contact form submissions.</p>
      </div>

      <!-- Inquiries List -->
      <div v-else class="divide-y divide-outline-variant/15">
        <div 
          v-for="inquiry in inquiries" 
          :key="inquiry.id"
          class="p-6 transition-colors duration-200 hover:bg-surface-container-low flex flex-col gap-4 group"
          :class="{ 'bg-primary/5': inquiry.status === 'new' }"
        >
          <!-- Header: Status, Date, Name -->
          <div class="flex justify-between items-start">
            <div class="flex items-center gap-3">
              <span v-if="inquiry.status === 'new'" class="w-2.5 h-2.5 rounded-full bg-primary mt-1"></span>
              <span v-else-if="inquiry.status === 'replied_staff'" class="w-2.5 h-2.5 rounded-full bg-green-500 mt-1" title="Replied by Staff"></span>
              <span v-else-if="inquiry.status === 'replied_director'" class="w-2.5 h-2.5 rounded-full bg-purple-500 mt-1" title="Replied by Director"></span>
              <div>
                <h4 class="text-lg font-semibold text-on-surface" :class="{'font-bold': inquiry.status === 'new'}">
                  {{ inquiry.name }}
                </h4>
                <a :href="'mailto:' + inquiry.email" class="text-sm text-primary hover:underline flex items-center gap-1 mt-0.5">
                  <span class="material-symbols-outlined text-[14px]">mail</span>
                  {{ inquiry.email }}
                </a>
              </div>
            </div>
            
            <div class="flex items-center gap-4">
              <span class="text-sm text-on-surface-variant bg-surface-container px-3 py-1 rounded-full whitespace-nowrap">
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
                class="text-sm px-3 py-1 bg-surface-container-low border border-outline-variant/50 rounded text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors opacity-0 group-hover:opacity-100 focus:opacity-100 flex items-center gap-1"
                title="Mark as Read"
              >
                <span class="material-symbols-outlined text-[16px]">done</span> Mark Read
              </button>

              <button 
                @click="deleteInquiry(inquiry)"
                class="text-sm px-3 py-1 bg-red-50 text-red-600 border border-red-200 rounded hover:bg-red-100 transition-colors opacity-0 group-hover:opacity-100 focus:opacity-100 flex items-center gap-1"
                title="Delete Inquiry"
              >
                <span class="material-symbols-outlined text-[16px]">delete</span> Delete
              </button>
            </div>
          </div>

          <!-- Content: Subject & Message -->
          <div class="ml-0 lg:ml-6 mt-2">
            <h5 class="font-medium text-on-surface mb-2">Subject: {{ inquiry.subject }}</h5>
            <div class="bg-surface-container rounded-lg p-4 border border-outline-variant/15 text-on-surface-variant text-sm whitespace-pre-wrap leading-relaxed">
              {{ inquiry.message }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Reply Modal -->
    <div v-if="showReplyModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-surface-container-lowest rounded-xl shadow-xl w-full max-w-lg flex flex-col">
        <div class="p-6 border-b border-outline-variant/15 flex justify-between items-center">
          <h3 class="text-xl font-bold text-on-surface">Reply to {{ activeInquiry?.name }}</h3>
          <button @click="closeReplyModal" class="text-on-surface-variant hover:text-on-surface transition-colors">
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
          
          <label class="block text-sm font-semibold text-on-surface mb-2">Message</label>
          <textarea 
            v-model="replyMessage" 
            rows="6" 
            class="w-full bg-surface-container-low text-on-surface border border-outline-variant/50 rounded-lg focus:ring-primary focus:border-primary p-3" 
            placeholder="Type your reply here..."
          ></textarea>
        </div>
        
        <div class="p-6 border-t border-outline-variant/15 bg-surface-container-low flex justify-end gap-3 rounded-b-xl">
          <button @click="closeReplyModal" class="px-4 py-2 text-on-surface-variant hover:bg-surface-container-high bg-surface-container rounded-lg font-medium transition-colors">
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

const deleteInquiry = async (inquiry) => {
  const result = await Swal.fire({
    title: 'Are you sure?',
    text: "This inquiry will be permanently deleted from the database. This action cannot be undone.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!'
  });

  if (result.isConfirmed) {
    try {
      const res = await api.delete(`/contact-inquiries/${inquiry.id}`);
      if (res.status === 200) {
        Swal.fire(
          'Deleted!',
          'The inquiry has been deleted.',
          'success'
        );
        inquiries.value = inquiries.value.filter(i => i.id !== inquiry.id);
      }
    } catch (error) {
      console.error('Error deleting inquiry:', error);
      Swal.fire(
        'Error!',
        'Failed to delete the inquiry.',
        'error'
      );
    }
  }
};

onMounted(() => {
  fetchInquiries();
});
</script>

<style scoped>
</style>
