/**
 * JAVASCRIPT TABEL PERSEDIAAN BARANG BPS KABUPATEN BANDUNG
 * Validasi, Kalkulasi Otomatis, dan Manajemen Modal
 */

function formatRupiah(angka) {
    if (isNaN(angka) || angka === null || angka === undefined) return 'Rp 0';
    return 'Rp ' + Math.round(angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Kalkulasi Otomatis Form Tambah
function hitungTotalTambah() {
    const hargaBeli = parseFloat(document.getElementById('tambah_harga_beli').value) || 0;
    const jmlPersediaan = parseInt(document.getElementById('tambah_total_persediaan_jumlah').value) || 0;
    const jmlRusak = parseInt(document.getElementById('tambah_rusak_jumlah').value) || 0;
    const jmlUsang = parseInt(document.getElementById('tambah_usang_jumlah').value) || 0;

    const totalPersediaan = hargaBeli * jmlPersediaan;
    const totalRusak = hargaBeli * jmlRusak;
    const totalUsang = hargaBeli * jmlUsang;

    document.getElementById('tambah_harga_total_persediaan_preview').innerText = formatRupiah(totalPersediaan);
    document.getElementById('tambah_rusak_harga_total_preview').innerText = formatRupiah(totalRusak);
    document.getElementById('tambah_usang_harga_total_preview').innerText = formatRupiah(totalUsang);
}

// Kalkulasi Otomatis Form Edit
function hitungTotalEdit() {
    const hargaBeli = parseFloat(document.getElementById('edit_harga_beli').value) || 0;
    const jmlPersediaan = parseInt(document.getElementById('edit_total_persediaan_jumlah').value) || 0;
    const jmlRusak = parseInt(document.getElementById('edit_rusak_jumlah').value) || 0;
    const jmlUsang = parseInt(document.getElementById('edit_usang_jumlah').value) || 0;

    const totalPersediaan = hargaBeli * jmlPersediaan;
    const totalRusak = hargaBeli * jmlRusak;
    const totalUsang = hargaBeli * jmlUsang;

    document.getElementById('edit_harga_total_persediaan_preview').innerText = formatRupiah(totalPersediaan);
    document.getElementById('edit_rusak_harga_total_preview').innerText = formatRupiah(totalRusak);
    document.getElementById('edit_usang_harga_total_preview').innerText = formatRupiah(totalUsang);
}

// Modal Tambah
function bukaModalTambah() {
    const modal = document.getElementById('modalTambahPersediaan');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        hitungTotalTambah();
    }
}

function tutupModalTambah() {
    const modal = document.getElementById('modalTambahPersediaan');
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
}

// Modal Edit
function bukaModalEdit(id, noUrut, nama, satuan, hargaBeli, jmlPersediaan, jmlRusak, jmlUsang, keterangan) {
    document.getElementById('formEditPersediaan').action = `/persediaan/edit/${id}`;
    document.getElementById('edit_nomor_urut').value = noUrut;
    document.getElementById('edit_uraian_barang').value = nama;
    document.getElementById('edit_satuan').value = satuan;
    document.getElementById('edit_harga_beli').value = hargaBeli;
    document.getElementById('edit_total_persediaan_jumlah').value = jmlPersediaan;
    document.getElementById('edit_rusak_jumlah').value = jmlRusak || 0;
    document.getElementById('edit_usang_jumlah').value = jmlUsang || 0;
    document.getElementById('edit_keterangan').value = keterangan || '';

    hitungTotalEdit();

    const modal = document.getElementById('modalEditPersediaan');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
}

function tutupModalEdit() {
    const modal = document.getElementById('modalEditPersediaan');
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
}

// Modal Hapus
function konfirmasiHapus(id, noUrut, nama) {
    document.getElementById('formHapusPersediaan').action = `/persediaan/hapus/${id}`;
    document.getElementById('hapus_nama_barang').innerText = `${noUrut}. ${nama}`;
    const modal = document.getElementById('modalHapusPersediaan');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
}

function tutupModalHapus() {
    const modal = document.getElementById('modalHapusPersediaan');
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
}

// Modal Edit Pejabat Penandatangan
function bukaModalEditPejabat() {
    const modal = document.getElementById('modalEditPejabatPersediaan');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
}

function tutupModalEditPejabat() {
    const modal = document.getElementById('modalEditPejabatPersediaan');
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
}

// Modal Hapus Semua Data Persediaan
function bukaModalHapusSemuaPersediaan() {
    const modal = document.getElementById('modalHapusSemuaPersediaan');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
}

function tutupModalHapusSemuaPersediaan() {
    const modal = document.getElementById('modalHapusSemuaPersediaan');
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
}

// Keyboard shortcuts (Escape key)
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        tutupModalTambah();
        tutupModalEdit();
        tutupModalHapus();
        tutupModalEditPejabat();
        tutupModalHapusSemuaPersediaan();
    }
});

