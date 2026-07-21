<template>
  <div class="venues-view">
    <div class="header-actions">
      <h2>Venues Management</h2>
      <button class="btn-add" @click="openAddModal">
        <span class="material-symbols-outlined">add</span>
        Add New Venue
      </button>
    </div>

    <!-- Error/Loading states -->
    <div v-if="loading" class="state-message">Loading venues...</div>
    <div v-else-if="error" class="state-message error">{{ error }}</div>
    
    <!-- Venues Table -->
    <div v-else class="table-container">
      <table class="venues-table">
        <thead>
          <tr>
            <th>Venue Name</th>
            <th>Location Type</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="venues.length === 0">
            <td colspan="3" class="empty-state">No venues found.</td>
          </tr>
          <tr v-for="venue in venues" :key="venue.venue_id">
            <td>{{ venue.venue_name }}</td>
            <td>
              <span class="badge" :class="venue.is_inside_bsu ? 'badge-inside' : 'badge-outside'">
                {{ venue.is_inside_bsu ? 'Inside BSU' : 'Outside BSU' }}
              </span>
            </td>
            <td class="actions-cell">
              <button class="action-btn edit-btn" @click="openEditModal(venue)" title="Edit">
                <span class="material-symbols-outlined">edit</span>
              </button>
              <button class="action-btn delete-btn" @click="confirmDelete(venue.venue_id)" title="Delete">
                <span class="material-symbols-outlined">delete</span>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal for Add/Edit -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <div class="modal-header">
          <h3>{{ editingVenue ? 'Edit Venue' : 'Add New Venue' }}</h3>
          <button class="close-btn" @click="closeModal">
            <span class="material-symbols-outlined">close</span>
          </button>
        </div>
        <form @submit.prevent="saveVenue" class="modal-form">
          <div class="form-group">
            <label>Venue Name *</label>
            <input type="text" v-model="form.venue_name" required placeholder="e.g. BSU Gymnasium" />
          </div>
          
          <div class="form-group checkbox-group">
            <label class="checkbox-label">
              <input type="checkbox" v-model="form.is_inside_bsu" />
              This venue is inside BSU campus
            </label>
          </div>

          <div v-if="formError" class="form-error">{{ formError }}</div>

          <div class="modal-actions">
            <button type="button" class="btn-cancel" @click="closeModal" :disabled="saving">Cancel</button>
            <button type="submit" class="btn-save" :disabled="saving">
              {{ saving ? 'Saving...' : 'Save Venue' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api'; // Assuming you have an api service

const venues = ref([]);
const loading = ref(true);
const error = ref('');
const showModal = ref(false);
const editingVenue = ref(null);
const saving = ref(false);
const formError = ref('');

const form = ref({
  venue_name: '',
  is_inside_bsu: true
});

const fetchVenues = async () => {
  loading.value = true;
  error.value = '';
  try {
    const res = await api.get('/venues');
    venues.value = res.data;
  } catch (err) {
    error.value = 'Failed to load venues. Please try again.';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const openAddModal = () => {
  editingVenue.value = null;
  form.value = { venue_name: '', is_inside_bsu: true };
  formError.value = '';
  showModal.value = true;
};

const openEditModal = (venue) => {
  editingVenue.value = venue;
  form.value = { 
    venue_name: venue.venue_name, 
    is_inside_bsu: venue.is_inside_bsu 
  };
  formError.value = '';
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const saveVenue = async () => {
  saving.value = true;
  formError.value = '';
  try {
    if (editingVenue.value) {
      // Update
      const res = await api.put(`/venues/${editingVenue.value.venue_id}`, form.value);
      const index = venues.value.findIndex(v => v.venue_id === editingVenue.value.venue_id);
      if (index !== -1) {
        venues.value[index] = res.data;
      }
    } else {
      // Create
      const res = await api.post('/venues', form.value);
      venues.value.push(res.data);
    }
    closeModal();
  } catch (err) {
    formError.value = err.response?.data?.messages?.error || 'Failed to save venue. It might already exist.';
    console.error(err);
  } finally {
    saving.value = false;
  }
};

const confirmDelete = async (id) => {
  if (confirm('Are you sure you want to delete this venue? It may break existing reports if they rely on it.')) {
    try {
      await api.delete(`/venues/${id}`);
      venues.value = venues.value.filter(v => v.venue_id !== id);
    } catch (err) {
      alert('Failed to delete venue. It might be in use.');
      console.error(err);
    }
  }
};

onMounted(() => {
  fetchVenues();
});
</script>

<style scoped>
.venues-view {
  padding: 32px;
  max-width: 1200px;
  margin: 0 auto;
}

.header-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

h2 {
  font-size: 24px;
  font-weight: 800;
  color: #1a1a2e;
  margin: 0;
}

.btn-add {
  display: flex;
  align-items: center;
  gap: 8px;
  background: linear-gradient(135deg, #990dd1, #b979cc);
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}
.btn-add:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(153, 13, 209, 0.3);
}

.table-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.venues-table {
  width: 100%;
  border-collapse: collapse;
}

.venues-table th, .venues-table td {
  padding: 16px;
  text-align: left;
  border-bottom: 1px solid #f1f5f9;
}

.venues-table th {
  background: #f8fafc;
  font-weight: 600;
  color: #475569;
  text-transform: uppercase;
  font-size: 13px;
  letter-spacing: 0.05em;
}

.badge {
  padding: 4px 12px;
  border-radius: 9999px;
  font-size: 12px;
  font-weight: 600;
}
.badge-inside {
  background: #dcfce7;
  color: #166534;
}
.badge-outside {
  background: #fef3c7;
  color: #92400e;
}

.actions-cell {
  display: flex;
  gap: 8px;
}

.action-btn {
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 6px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
}

.edit-btn { color: #3b82f6; }
.edit-btn:hover { background: #eff6ff; }
.delete-btn { color: #ef4444; }
.delete-btn:hover { background: #fef2f2; }

/* Modal Styles */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  width: 100%;
  max-width: 500px;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}
.modal-header h3 { margin: 0; font-size: 20px; color: #1a1a2e; }
.close-btn { background: transparent; border: none; cursor: pointer; color: #64748b; }

.form-group { margin-bottom: 20px; display: flex; flex-direction: column; gap: 8px; }
.form-group label { font-weight: 600; color: #334155; font-size: 14px; }
.form-group input[type="text"] {
  padding: 10px 12px;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  font-size: 15px;
  outline: none;
  transition: border-color 0.2s;
}
.form-group input[type="text"]:focus { border-color: #990dd1; }

.checkbox-group { flex-direction: row; align-items: center; }
.checkbox-label {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  font-weight: normal !important;
}

.form-error { color: #ef4444; font-size: 14px; margin-bottom: 20px; }

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 32px;
}
.btn-cancel {
  padding: 10px 20px;
  border: 1px solid #cbd5e1;
  background: transparent;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  color: #475569;
}
.btn-save {
  padding: 10px 20px;
  background: #990dd1;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
}
.btn-save:disabled { opacity: 0.7; cursor: not-allowed; }
</style>
