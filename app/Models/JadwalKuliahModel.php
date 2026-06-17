<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalKuliahModel extends Model
{
    protected $table            = 'jadwal_kuliah';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['kelas_id', 'pertemuan_ke', 'tanggal', 'jam_mulai', 'jam_selesai', 'ruangan', 'created_at'];

    protected $useTimestamps    = false;
    protected $skipValidation   = true;

    /**
     * Memeriksa apakah ada konflik penggunaan ruangan pada waktu yang sama
     * 
     * @param string $ruangan
     * @param string $tanggal (YYYY-MM-DD)
     * @param string $jam_mulai (HH:MM:SS)
     * @param string $jam_selesai (HH:MM:SS)
     * @param int|null $ignore_id
     * @return array|null Mengembalikan record jadwal yang konflik jika ada, null jika aman
     */
    public function cekKonflikRuangan($ruangan, $tanggal, $jam_mulai, $jam_selesai, $ignore_id = null)
    {
        $builder = $this->select('jadwal_kuliah.*, mata_kuliah.nama as nama_mk')
                        ->join('kelas', 'kelas.id = jadwal_kuliah.kelas_id')
                        ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                        ->where('jadwal_kuliah.ruangan', $ruangan)
                        ->where('jadwal_kuliah.tanggal', $tanggal);

        if ($ignore_id !== null) {
            $builder->where('jadwal_kuliah.id !=', $ignore_id);
        }

        // Kondisi overlap: jam_mulai < jam_selesai_existing DAN jam_selesai > jam_mulai_existing
        $builder->where('jadwal_kuliah.jam_mulai <', $jam_selesai)
                ->where('jadwal_kuliah.jam_selesai >', $jam_mulai);

        return $builder->first();
    }

    /**
     * Ambil jadwal kuliah berdasarkan kelas_id
     */
    public function getJadwalByKelas($kelas_id)
    {
        return $this->where('kelas_id', $kelas_id)
                    ->orderBy('pertemuan_ke', 'ASC')
                    ->findAll();
    }
}
