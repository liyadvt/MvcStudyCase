<?php 

class BarangModel{
    
    private $table = 'tb_peminjaman';
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getAllBarang()
    {
        $this->db->query("SELECT * FROM " . $this->table);
        return $this->db->resultSet();
    }

    public function getBarangById($id)
    {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE id=:id');
        $this->db->bind('id',$id);
        return $this->db->single();
    }

    public function tambahBarang($data)
    {
        $data['tgl_kembali'] = date('Y-m-d H:i:s', strtotime($data['tgl_pinjam'] . ' +2 days'));
        $data['status'] = "belum kembali";

        $query = "INSERT INTO tb_peminjaman (nama_peminjam, jenis_barang, no_barang, tgl_pinjam, tgl_kembali, status) VALUES (:nama_peminjam, :jenis_barang, :no_barang, :tgl_pinjam, :tgl_kembali, :status)";
        $this->db->query($query);
        $this->db->bind('nama_peminjam', $data['nama_peminjaman']);
        $this->db->bind('jenis_barang', $data['jenis_barang']);
        $this->db->bind('no_barang', $data['no_barang']);
        $this->db->bind('tgl_pinjam', $data['tgl_pinjam']);
        $this->db->bind('tgl_kembali', $data['tgl_kembali']);
        $this->db->bind('status', $data['status']);
        $this->db->execute();

        return $this->db->rowCount();
    }

    public function updateDataBarang($data)
    {
 
    // Mengambil data lama
    $lamasibarang = $this->getBarangById($data['id']);
    $tglbalik = $lamasibarang['tgl_kembali'];

    // Konversi tanggal ke format DateTime
    $tglKembaliBaru = new DateTime($data['tgl_kembali']);
    $tglPinjam = new DateTime($data['tgl_pinjam']);

    if ($tglKembaliBaru < $tglPinjam) {
        // Tanggal kembali baru lebih awal dari tanggal pinjam, tampilkan pesan kesalahan
        echo '<script>alert("Error: Tanggal kembali tidak boleh lebih awal dari tanggal pinjam.");</script>';
        return 0; // Tidak melakukan pembaruan jika ada kesalahan
    }

    if ($tglKembaliBaru != $tglbalik) {
        // Jika tanggal kembali baru berbeda dari yang lama
        $query = "UPDATE tb_peminjaman SET nama_peminjam=:nama_peminjam, jenis_barang=:jenis_barang, no_barang=:no_barang, tgl_pinjam=:tgl_pinjam, tgl_kembali=:tgl_kembali, status='Sudah Kembali' WHERE id=:id";
    } else {
        // Jika tanggal kembali baru sama dengan yang laxma
        $query = "UPDATE tb_peminjaman SET nama_peminjam=:nama_peminjam, jenis_barang=:jenis_barang, no_barang=:no_barang, tgl_pinjam=:tgl_pinjam, tgl_kembali=:tgl_kembali WHERE id=:id";
    }

        $query = "UPDATE tb_peminjaman SET nama_peminjam=:nama_peminjam, jenis_barang=:jenis_barang, no_barang=:no_barang, tgl_pinjam=:tgl_pinjam, tgl_kembali=:tgl_kembali, status=:status WHERE id=:id";
        $this->db->query($query);
        $this->db->bind('id', $data['id']);
        $this->db->bind('nama_peminjam', $data['nama_peminjam']);
        $this->db->bind('jenis_barang', $data['jenis_barang']);
        $this->db->bind('no_barang', $data['no_barang']);
        $this->db->bind('tgl_pinjam', $data['tgl_pinjam']);
        $this->db->bind('tgl_kembali', $data['tgl_kembali']);
        $this->db->bind('status', $data['status']);
        $this->db->execute();

        return $this->db->rowCount();
    }

    public function deleteBarang($id)
    {
        $this->db->query('DELETE FROM ' . $this->table . ' WHERE id=:id');
        $this->db->bind('id', $id);
        $this->db->execute();

        return $this->db->rowCount();
    }
    
    public function cariBarang($barang)
    {
        $this->db->query("SELECT * FROM " . $this->table . " WHERE nama_peminjam LIKE :nama_peminjam OR jenis_barang LIKE :jenis_barang");
        $this->db->bind('nama_peminjam', '%' . $barang . "%");
        $this->db->bind('jenis_barang', '%' . $barang . "%");
        return $this->db->resultSet();
    }
}
?>