<template>
  <div class="page-container">
    <div class="header-section">
      <h1 class="page-title">Activity Logs</h1>
      <p class="page-subtitle">Track and monitor all user activities within the system.</p>
    </div>

    <!-- Stats / Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
      <div class="stat-card">
        <div class="stat-icon bg-purple-500/20 text-purple-400">
          <span class="material-symbols-outlined">history</span>
        </div>
        <div class="stat-content">
          <h3 class="stat-label">Total Activities Recorded</h3>
          <p class="stat-value">{{ allLogs.length }}</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon bg-blue-500/20 text-blue-400">
          <span class="material-symbols-outlined">person_search</span>
        </div>
        <div class="stat-content">
          <h3 class="stat-label">Recent Active Users</h3>
          <p class="stat-value">{{ uniqueActiveUsers }}</p>
        </div>
      </div>
    </div>

    <div class="layout-grid">
      <!-- Left Column: Activity Logs List -->
      <section class="flex-06 glass-card">
        <div class="card-header border-b border-purple-900/30 p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
          <h2 class="text-xl font-bold text-white flex items-center gap-2">
            <span class="material-symbols-outlined text-purple-400">list_alt</span>
            Detailed Activity Logs
          </h2>
          
          <div class="filters-container w-full md:w-auto flex flex-col sm:flex-row gap-3">
            <select v-model="roleFilter" class="filter-select">
              <option value="">All Roles</option>
              <option value="admin">Director / Admin</option>
              <option value="gad_staff">GAD Staff</option>
              <option value="twg">TWG</option>
              <option value="non_twg">Non-TWG</option>
            </select>
            
            <div class="relative flex-grow" v-if="roleFilter === 'twg' || roleFilter === 'non_twg'">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
              <input 
                type="text" 
                v-model="emailSearch"
                placeholder="Search by email..."
                class="search-input"
              >
            </div>
          </div>
        </div>
        
        <div class="p-6">
          <div v-if="loading" class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-500"></div>
          </div>
          
          <div v-else-if="filteredLogs.length === 0" class="text-center py-12 text-slate-400">
            <span class="material-symbols-outlined text-4xl mb-2 opacity-50">inbox</span>
            <p>No activity logs found matching your filters.</p>
          </div>
          
          <div v-else class="space-y-4 max-h-[600px] overflow-y-auto custom-scrollbar pr-2">
            <div v-for="log in filteredLogs" :key="log.id" class="log-item">
              <div class="log-icon" :class="getActionColor(log.action)">
                <span class="material-symbols-outlined text-sm">{{ getActionIcon(log.action) }}</span>
              </div>
              <div class="log-details flex-grow">
                <p class="log-description">
                  <span class="font-bold text-white">{{ log.full_name }}</span> 
                  <span class="text-slate-300">{{ log.description }}</span>
                </p>
                <div class="log-meta flex items-center gap-4 mt-1">
                  <span class="text-xs text-slate-400 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">schedule</span>
                    {{ formatDateTime(log.created_at) }}
                  </span>
                  <span class="text-xs text-purple-400 font-medium px-2 py-0.5 bg-purple-500/10 rounded-full border border-purple-500/20">
                    {{ formatRole(log.role) }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Right Column: Recent Activities -->
      <section class="flex-04">
        <div class="glass-card h-full">
          <div class="card-header border-b border-purple-900/30 p-6">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
              <span class="material-symbols-outlined text-pink-400">bolt</span>
              Recent Activity
            </h2>
            <p class="text-xs text-slate-400 mt-1">Latest system actions across all users.</p>
          </div>
          
          <div class="p-6">
            <div class="relative border-l border-slate-700 ml-3 space-y-6">
              <div v-for="log in recentLogs.slice(0, 10)" :key="'recent-'+log.id" class="relative pl-6">
                <div class="absolute -left-[5px] top-1.5 w-2.5 h-2.5 rounded-full bg-slate-800 border-2 border-pink-500"></div>
                <div class="text-xs text-slate-400 mb-0.5">{{ formatTimeAgo(log.created_at) }}</div>
                <div class="text-sm font-medium text-white mb-1">{{ log.full_name }}</div>
                <div class="text-xs text-slate-300">{{ log.action }}</div>
              </div>
              <div v-if="recentLogs.length === 0 && !loading" class="text-slate-400 text-sm pl-6 py-4">
                No recent activity.
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../api';

const router = useRouter();
const user = ref(JSON.parse(localStorage.getItem('user') || '{}'));
const allLogs = ref([]);
const loading = ref(true);

const roleFilter = ref('');
const emailSearch = ref('');

const fetchLogs = async () => {
  loading.value = true;
  try {
    const res = await api.get('/activity-logs', { params: { role: 'admin' } });
    if (res.data.success) {
      allLogs.value = res.data.data;
    }
  } catch (err) {
    console.error('Error fetching logs', err);
  } finally {
    loading.value = false;
  }
};

const recentLogs = computed(() => {
  // Sort by created_at desc
  return [...allLogs.value].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
});

const filteredLogs = computed(() => {
  let logs = recentLogs.value;
  
  if (roleFilter.value) {
    if (roleFilter.value === 'twg') {
      logs = logs.filter(l => l.role === 'TWG');
    } else if (roleFilter.value === 'non_twg') {
      logs = logs.filter(l => l.role === 'Non-TWG');
    } else {
      logs = logs.filter(l => l.role?.toLowerCase() === roleFilter.value);
    }
  }
  
  if ((roleFilter.value === 'twg' || roleFilter.value === 'non_twg') && emailSearch.value) {
    const term = emailSearch.value.toLowerCase();
    logs = logs.filter(l => l.email && l.email.toLowerCase().includes(term));
  }
  
  return logs;
});

const uniqueActiveUsers = computed(() => {
  const users = new Set(allLogs.value.map(l => l.user_id));
  return users.size;
});

const formatRole = (role) => {
  if (!role) return 'Unknown';
  if (role === 'admin') return 'Admin';
  if (role === 'director') return 'Director';
  if (role === 'gad_staff') return 'Staff';
  return role;
};

const formatDateTime = (dateStr) => {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  return d.toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true });
};

const formatTimeAgo = (dateStr) => {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  const now = new Date();
  const seconds = Math.floor((now - date) / 1000);
  
  if (seconds < 60) return 'Just now';
  const minutes = Math.floor(seconds / 60);
  if (minutes < 60) return `${minutes}m ago`;
  const hours = Math.floor(minutes / 60);
  if (hours < 24) return `${hours}h ago`;
  const days = Math.floor(hours / 24);
  if (days < 7) return `${days}d ago`;
  return formatDateTime(dateStr);
};

const getActionIcon = (action) => {
  const map = {
    'Login': 'login',
    'Submit Document': 'post_add',
    'Approve Document': 'check_circle',
    'Update Status': 'edit_note',
    'Update Deadline': 'event',
    'Trash Document': 'delete',
    'Cancel Document': 'cancel',
    'Send Message': 'send',
    'Trash Message': 'delete_outline',
    'Suspend User': 'block',
    'Restore User': 'settings_backup_restore',
    'Delete User': 'person_remove',
    'Register User': 'person_add'
  };
  return map[action] || 'history';
};

const getActionColor = (action) => {
  const map = {
    'Login': 'bg-green-500/20 text-green-400 border-green-500/30',
    'Submit Document': 'bg-blue-500/20 text-blue-400 border-blue-500/30',
    'Approve Document': 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
    'Trash Document': 'bg-red-500/20 text-red-400 border-red-500/30',
    'Cancel Document': 'bg-orange-500/20 text-orange-400 border-orange-500/30',
    'Send Message': 'bg-indigo-500/20 text-indigo-400 border-indigo-500/30',
    'Register User': 'bg-teal-500/20 text-teal-400 border-teal-500/30',
    'Delete User': 'bg-red-500/20 text-red-400 border-red-500/30'
  };
  return map[action] || 'bg-slate-700 text-slate-300 border-slate-600';
};

onMounted(() => {
  if (!user.value.id || user.value.role !== 'admin') {
    router.push('/login');
  } else {
    fetchLogs();
  }
});
</script>

<style scoped>
.page-container {
  padding: 1rem;
  max-width: 1400px;
  margin: 0 auto;
}

.header-section {
  margin-bottom: 2rem;
}

.page-title {
  font-family: 'Times New Roman', serif;
  font-size: 2rem;
  color: white;
  margin-bottom: 0.5rem;
}

.page-subtitle {
  color: #94a3b8;
  font-size: 0.95rem;
}

.stat-card {
  background: linear-gradient(135deg, rgba(30, 41, 59, 0.7) 0%, rgba(15, 23, 42, 0.7) 100%);
  border: 1px solid rgba(185, 121, 204, 0.2);
  border-radius: 1rem;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1.5rem;
  backdrop-filter: blur(12px);
}

.stat-icon {
  width: 3.5rem;
  height: 3.5rem;
  border-radius: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-icon span {
  font-size: 1.75rem;
}

.stat-label {
  color: #cbd5e1;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-weight: 600;
  margin-bottom: 0.25rem;
}

.stat-value {
  color: white;
  font-size: 1.75rem;
  font-weight: 700;
  line-height: 1;
}

.layout-grid {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

@media (min-width: 1024px) {
  .layout-grid {
    flex-direction: row;
  }
  .flex-06 { flex: 0.65; }
  .flex-04 { flex: 0.35; }
}

.glass-card {
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(12px);
  border-radius: 1.25rem;
  border: 1px solid rgba(185, 121, 204, 0.15);
  box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
  overflow: hidden;
}

.filter-select, .search-input {
  background: rgba(0, 0, 0, 0.2);
  border: 1px solid rgba(185, 121, 204, 0.3);
  color: white;
  padding: 0.6rem 1rem;
  border-radius: 0.75rem;
  font-size: 0.875rem;
  transition: all 0.2s;
}

.search-input {
  padding-left: 2.25rem;
  width: 100%;
}

.filter-select:focus, .search-input:focus {
  outline: none;
  border-color: #b979cc;
  background: rgba(0, 0, 0, 0.4);
}

.log-item {
  display: flex;
  gap: 1rem;
  padding: 1.25rem;
  background: rgba(255, 255, 255, 0.02);
  border-radius: 1rem;
  border: 1px solid transparent;
  transition: all 0.2s;
}

.log-item:hover {
  background: rgba(255, 255, 255, 0.04);
  border-color: rgba(185, 121, 204, 0.2);
  transform: translateX(4px);
}

.log-icon {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 0.75rem;
  border: 1px solid;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.log-description {
  font-size: 0.95rem;
  line-height: 1.4;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.1);
  border-radius: 8px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(185, 121, 204, 0.3);
  border-radius: 8px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(185, 121, 204, 0.5);
}
</style>
