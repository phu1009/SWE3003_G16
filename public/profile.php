<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>My profile</title>

  <!-- Bootstrap & Vue -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://unpkg.com/vue@3.4.21/dist/vue.global.prod.js"></script>
</head>
<body>
<?php include __DIR__ . '/navbar.php'; ?>

<div id="app" class="container">
  <h2 class="mb-4">My profile</h2>

  <!-- AVATAR ---------------------------------------------------------->
  <div class="d-flex align-items-center mb-4">
    <img :src="avatarPreview" class="rounded-circle me-3 border"
         style="width:96px;height:96px;object-fit:cover">
    <div>
      <input @change="pickAvatar" type="file" accept="image/*" class="form-control-sm">
      <small class="text-muted">JPG/PNG ≤ 5 MB</small>
    </div>
  </div>

  <!-- PROFILE FORM ---------------------------------------------------->
  <form @submit.prevent="save">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Email
          <input v-model="form.email" type="email" class="form-control" required>
        </label>
      </div>
      <div class="col-md-6">
        <label class="form-label">Phone
          <input v-model="form.phone" class="form-control">
        </label>
      </div>
    </div>

    <!-- MAIN ADDRESS -->
    <h5 class="mt-4">Primary address</h5>

    <label class="form-label">Address name
      <input v-model="form.address_name" class="form-control" placeholder="Home">
    </label>

    <div class="row g-2 mt-2">
      <div class="col-md-8">
        <input v-model="form.address_line1" class="form-control"
               placeholder="Street & number" required>
      </div>
      <div class="col-md-4">
        <input v-model="form.address_line2" class="form-control"
               placeholder="Apartment / ward">
      </div>
    </div>

    <div class="row g-2 mt-2">
      <div class="col-md-4"><input v-model="form.city"  class="form-control" placeholder="City"></div>
      <div class="col-md-4"><input v-model="form.state" class="form-control" placeholder="State / region"></div>
      <div class="col-md-4"><input v-model="form.postal_code" class="form-control" placeholder="Postal code"></div>
    </div>

    <div class="mt-4">
      <button class="btn btn-primary">Save changes</button>
    </div>
  </form>

  <!-- EXTRA ADDRESSES ------------------------------------------------
  <h4 class="mt-5 d-flex justify-content-between align-items-center">
    Additional addresses
    <button class="btn btn-sm btn-outline-primary"
            :disabled="addresses.length >= 4"
            @click="openAddrModal">
      + Add address
    </button>
  </h4>

  <table class="table table-hover align-middle mt-2" v-if="addresses.length">
    <thead class="table-light">
      <tr><th>Name</th><th>Address</th><th class="text-end" style="width:110px"></th></tr>
    </thead>
    <tbody>
      <tr v-for="a in addresses" :key="a.address_id">
        <td>{{ a.address_name }}</td>
        <td>
          {{ a.address_line1 }},
          {{ a.address_line2 ? a.address_line2 + ',' : '' }}
          {{ a.city }}, {{ a.state_region }} {{ a.postal_code }}
        </td>
        <td class="text-end">
          <button class="btn btn-sm btn-outline-primary me-1" @click="editAddr(a)">Edit</button>
          <button class="btn btn-sm btn-outline-danger"  @click="deleteAddr(a)">Del</button>
        </td>
      </tr>
    </tbody>
  </table>
  <p v-else class="text-muted">No additional addresses saved.</p>
</div>
-------------------------------------->
<!-- ADD / EDIT MODAL -------------------------------------------------->
<div class="modal fade" id="addrModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title">{{ addr.address_id ? 'Edit address' : 'Add address' }}</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    <form @submit.prevent="saveAddr">
      <div class="modal-body">

        <label class="form-label">Address name
          <input v-model="addr.address_name" class="form-control" required>
        </label>

        <label class="form-label mt-2">Address line 1
          <input v-model="addr.address_line1" class="form-control" required>
        </label>
        <label class="form-label mt-2">Address line 2
          <input v-model="addr.address_line2" class="form-control">
        </label>

        <div class="row g-2 mt-2">
          <div class="col-md-6"><input v-model="addr.city"  class="form-control" placeholder="City"></div>
          <div class="col-md-6"><input v-model="addr.state" class="form-control" placeholder="State / region"></div>
        </div>

        <div class="row g-2 mt-2">
          <div class="col-md-6"><input v-model="addr.postal_code" class="form-control" placeholder="Postal code"></div>
          <div class="col-md-6">
            <select v-model="addr.country" class="form-select">
              <option>Vietnam</option>
              <option>USA</option>
              <option>Singapore</option>
              <!-- add more as needed -->
            </select>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary">Save address</button>
      </div>
    </form>
  </div></div>
</div>

<!-- JS ---------------------------------------------------------------->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const { createApp, reactive, ref, onMounted } = Vue;

createApp({
  setup () {
    /* ---------- state ---------- */
    const PLACEHOLDER = 'images/avatars/default.png';
    const form      = reactive({});
    const addresses = ref([]);
    const avatarPreview = ref(PLACEHOLDER);
    let   newAvatar = null;

    const addr      = reactive({});
    let   bsModal;
    const MAX_EXTRA = 4;

    /* ---------- helpers ---------- */
    function pickAvatar (e) {
      newAvatar = e.target.files[0] || null;
      if (newAvatar) avatarPreview.value = URL.createObjectURL(newAvatar);
    }

    /* ---------- API ---------- */
    async function load () {
      const r  = await fetch('api/modules/user/profile_get.php');
      const d  = await r.json();
      Object.assign(form, d);
      addresses.value = d.addresses || [];
      avatarPreview.value = d.avatar_path
          ? d.avatar_path.replace(/^\/+/, '')
          : PLACEHOLDER;
    }

    async function save () {
      const fd = new FormData();
      Object.entries(form).forEach(([k,v]) => fd.append(k,v ?? ''));
      if (newAvatar) fd.append('avatar', newAvatar);

      const r  = await fetch('api/modules/user/profile_save.php',{method:'POST',body:fd});
      const j  = await r.json();
      if (j.ok) alert('Profile saved'); else alert(j.error || 'Save failed');
    }

    /* ---------- extra addresses ---------- */
    function openAddrModal () {
      if (addresses.value.length >= MAX_EXTRA) {
        alert('You can only have four additional addresses.');
        return;
      }
      Object.assign(addr,{
        address_id:'', address_name:'', address_line1:'', address_line2:'',
        city:'', state:'', postal_code:'', country:'Vietnam'
      });
      bsModal.show();
    }

    function editAddr (a) {
      Object.assign(addr, JSON.parse(JSON.stringify(a))); // deep copy
      bsModal.show();
    }

    async function saveAddr () {
      const fd = new FormData();
      Object.entries(addr).forEach(([k,v])=>fd.append(k,v));
      const api = addr.address_id
          ? 'api/modules/user/address_update.php'
          : 'api/modules/user/address_add.php';

      const r  = await fetch(api,{method:'POST',body:fd});
      const j  = await r.json();
      if (r.status === 409) { alert(j.error); return; }
      if (j.ok) { bsModal.hide(); load(); }
      else alert(j.error || 'Save failed');
    }

    async function deleteAddr (a) {
      if (!confirm('Delete this address?')) return;
      const fd = new FormData(); fd.append('address_id', a.address_id);
      const r  = await fetch('api/modules/user/address_delete.php',{method:'POST',body:fd});
      const j  = await r.json();
      if (j.ok) load(); else alert(j.error || 'Delete failed');
    }

    /* ---------- mount ---------- */
    onMounted(()=>{
      load();
      bsModal = new bootstrap.Modal('#addrModal');
    });

    /* ---------- expose ---------- */
    return { form, avatarPreview, pickAvatar, save,
             addresses, openAddrModal, editAddr, deleteAddr,
             addr, saveAddr };
  }
}).mount('#app');
</script>
</body>
</html>
