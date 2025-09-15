// Format a number as currency




// Format a number with separators
function formatNumber(value) {
    // Hapus semua karakter non-angka dan non-koma (jika ada)
    let num = value.replace(/[^0-9,.]/g, '');

    // Ubah koma menjadi titik untuk validasi float
    num = num.replace(',', '.');

    // Pisahkan bagian integer dan desimal
    let parts = num.split('.');
    let integerPart = parts[0];
    let decimalPart = parts[1] || '';

    // Tambahkan separator ribuan
    integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    // Gabungkan kembali bagian integer dan desimal (jika ada)
    if (decimalPart) {
      return integerPart + ',' + decimalPart;
    } else {
      return integerPart;
    }
  }