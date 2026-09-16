<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeaveType;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        LeaveType::updateOrCreate(
            ['code' => 'CT_TAHUNAN'],
            [
                'name' => 'Cuti Tahunan',
                'description' => 'Cuti Tahunan untuk prajurit TNI AD yang telah memenuhi masa dinas.',
                'terms_conditions' => "• Prajurit TNI AD telah berdinas sekurang-kurangnya 1 tahun terus-menerus.\n• 12 hari kerja setiap tahun.\n• Hari libur tidak dihitung sebagai hari cuti.\n• Dapat dibagi menjadi 2 tahap, masing-masing maksimal 6 hari kerja.\n• Jarak antara tahap pertama dan kedua sekurang-kurangnya 6 bulan.\n• Cuti dapat ditambah waktu perjalanan pulang-pergi untuk daerah sulit transportasi.\n• Tambahan waktu perjalanan maksimal 7 hari.\n• Apabila ada kepentingan dinas maka:\n  1. Pelaksanaan cuti dapat ditunda.\n  2. Cuti yang telah diberikan/dijalankan dapat ditarik kembali karena kepentingan dinas.\n  3. Penundaan/penarikan harus dilakukan secara tertulis dan disertai alasan.\n• Cuti tahunan yang tidak dilaksanakan tidak dapat digunakan pada tahun berikutnya.\n• Bagi pelatih/gumil di lembaga pendidikan militer, pelaksanaannya disesuaikan dengan masa liburan lembaga pendidikan.",
                'required_documents_info' => 'Scan/Foto Surat Permohonan Cuti & Nota Dinas Atasan Langsung.',
                'default_days' => 12,
                'is_active' => true,
            ]
        );

        LeaveType::updateOrCreate(
            ['code' => 'CT_SAKIT'],
            [
                'name' => 'Cuti Sakit',
                'description' => 'Cuti Sakit untuk prajurit yang mengalami gangguan kesehatan.',
                'terms_conditions' => "• Sakit sampai 2 hari berturut-turut cukup menyampaikan surat pemberitahuan sakit kepada Dansatminkal.\n• Lebih dari 2 hari harus disertai surat keterangan sakit dari dokter yang berdinas di lingkungan TNI AD.\n• Lebih dari 30 hari harus ada surat keputusan yang dikeluarkan oleh pejabat yang berwenang.\n• Jika masih sakit, dapat diperpanjang secara bertahap setiap 1 bulan sampai maksimal 6 bulan.\n• Cuti sakit diberikan paling lama 6 bulan.\n• Jika diperlukan, dapat diperpanjang paling lama 6 bulan lagi.",
                'required_documents_info' => 'Surat Keterangan Sakit dari Dokter/Rumah Sakit.',
                'default_days' => 3, 
                'is_active' => true,
            ]
        );

        LeaveType::updateOrCreate(
            ['code' => 'CT_KAWIN'],
            [
                'name' => 'Cuti Kawin',
                'description' => 'Cuti Kawin untuk pelaksanaan pernikahan prajurit.',
                'terms_conditions' => "• Prajurit TNI AD pria 3 hari kerja.\n• Prajurit TNI AD wanita 6 hari kerja.\n• Berlaku untuk pernikahan di tempat kedudukan/daerah tempat yang bersangkutan bertugas.\n• Jika pernikahan dilaksanakan di luar daerah penugasan, cuti dapat ditambah waktu perjalanan pulang-pergi.\n• Tambahan waktu perjalanan maksimal 7 hari berdasarkan pertimbangan pejabat yang berwenang.",
                'required_documents_info' => 'Surat Izin Nikah / Dokumen Persetujuan Komandan.',
                'default_days' => 6,
                'is_active' => true,
            ]
        );

        LeaveType::updateOrCreate(
            ['code' => 'CT_LUAR_BIASA'],
            [
                'name' => 'Cuti Luar Biasa',
                'description' => 'Cuti Luar Biasa untuk keperluan mendesak atau kepentingan keluarga.',
                'terms_conditions' => "• 8 hari kerja dalam 1 tahun.\n• Dapat digunakan sekaligus atau terputus-putus sesuai kebutuhan.\n• Dapat diberikan untuk:\n  1. Memenuhi kewajiban hukum yang tidak dapat dilakukan di luar jam dinas.\n  2. Memenuhi panggilan wajib sebagai tersangka atau saksi dalam suatu perkara.\n  3. Suami/istri, anak, ibu/bapak kandung/tiri, mertua, atau saudara kandung sakit keras atau meninggal dunia.\n  4. Anggota keluarga lainnya meninggal dunia dan pemakaman harus diurus oleh prajurit.\n  5. Istri melahirkan.\n• Jika melebihi 8 hari kerja dalam 1 tahun, kelebihan hari tersebut akan mengurangi jatah cuti tahunan pada tahun yang sama.\n• Khusus kondisi tertentu akibat anggota keluarga meninggal dapat diperpanjang maksimal 1 bulan dalam 1 tahun apabila prajurit harus mengurus hak-hak terkait harta peninggalan sehingga sering meninggalkan tempat kedudukan.",
                'required_documents_info' => 'Surat Permohonan Khusus dan Bukti Kejadian (jika ada).',
                'default_days' => 8,
                'is_active' => true,
            ]
        );

        LeaveType::updateOrCreate(
            ['code' => 'CT_ISTIMEWA'],
            [
                'name' => 'Cuti Istimewa',
                'description' => 'Cuti Istimewa setelah melaksanakan tugas operasi atau pendidikan.',
                'terms_conditions' => "• 6 hari kerja, apabila pelaksanaan tugas/pendidikan berlangsung 3–6 bulan.\n• 12 hari kerja, apabila pelaksanaan tugas/pendidikan berlangsung lebih dari 6 bulan.\n• Diberikan kepada prajurit setelah melaksanakan tugas operasi, melaksanakan tugas luar negeri, mengikuti pendidikan.",
                'required_documents_info' => 'Surat Keterangan Kematian/Dokter/Lurah/Kepala Desa.',
                'default_days' => 6,
                'is_active' => true,
            ]
        );

        LeaveType::updateOrCreate(
            ['code' => 'CT_HAJI'],
            [
                'name' => 'Cuti Ibadah Haji',
                'description' => 'Cuti Ibadah Haji untuk pelaksanaan ibadah haji.',
                'terms_conditions' => "• Haji biasa maksimal 45 hari kerja.\n• Haji khusus (plus) maksimal 25 hari kerja.\n• Hari libur dalam tahun almanak tidak dihitung.\n• Telah berdinas 1 tahun terus-menerus.\n• Belum pernah melaksanakan ibadah haji.\n• Bagi yang sudah pernah melaksanakan haji, dapat melaksanakan kembali setelah 3 tahun kemudian.\n• Jika melaksanakan cuti haji, cuti tahunan dan cuti dinas lama pada tahun berjalan dihapuskan.",
                'required_documents_info' => 'Bukti Pendaftaran Haji / Jadwal Keberangkatan / Surat Rekomendasi.',
                'default_days' => 45,
                'is_active' => true,
            ]
        );

        LeaveType::updateOrCreate(
            ['code' => 'CT_UMROH_LAINNYA'],
            [
                'name' => 'Cuti Ibadah Umroh dan Ibadah Lainnya',
                'description' => 'Cuti Ibadah Umroh dan Ibadah Keagamaan Lainnya.',
                'terms_conditions' => "• Lama cuti maksimal 14 hari.\n• Cuti ini mengurangi/menghilangkan jatah cuti tahunan pada tahun berjalan.\n• Ketentuan/persyaratan lainnya mengikuti ketentuan Cuti Ibadah Haji.\n• Persyaratan masa dinas dan ketentuan bagi yang pernah melaksanakan ibadah tersebut mengikuti ketentuan yang berlaku pada cuti haji.",
                'required_documents_info' => 'Bukti dari Biro Perjalanan / Surat Rekomendasi Instansi Agama.',
                'default_days' => 14,
                'is_active' => true,
            ]
        );

        LeaveType::updateOrCreate(
            ['code' => 'CT_HAMIL_LAHIR'],
            [
                'name' => 'Cuti Hamil dan Melahirkan',
                'description' => 'Cuti Hamil dan Melahirkan untuk prajurit TNI AD wanita.',
                'terms_conditions' => "• Diberikan kepada prajurit TNI AD wanita yang telah melaksanakan perkawinan dan kemudian hamil serta melahirkan.\n• Lama cuti 90 hari dalam tahun almanak.\n• Pelaksanaan berdasarkan rekomendasi dokter yang bertugas di lingkungan TNI AD.\n• Jika melahirkan tetapi anak meninggal dunia atau mengalami keguguran sebelum waktunya maka diberikan istirahat 45 hari dalam tahun almanak setelah kejadian tersebut.\n• Harus berdasarkan surat keterangan dokter atau bidan yang bertugas di lingkungan TNI AD.\n• Berdasarkan pertimbangan medis dan surat keterangan dokter, waktu istirahat dapat diperpanjang maksimal 45 hari dalam tahun almanak.\n• Selama cuti/istirahat tetap menerima penghasilan penuh beserta tunjangan sesuai ketentuan.\n• Jika melaksanakan cuti hamil dan melahirkan cuti tahunan dan cuti dinas lama pada tahun berjalan dihapuskan.",
                'required_documents_info' => 'Surat Keterangan Dokter Kandungan / Bidan.',
                'default_days' => 90,
                'is_active' => true,
            ]
        );
    }
}
