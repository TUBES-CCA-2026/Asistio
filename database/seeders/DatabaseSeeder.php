<?php
namespace Database\Seeders;
use App\Models\{Role,User,MataKuliah,Ruangan,Dosen,Asisten,Praktikum,Mahasiswa,Presensi,NilaiAsistensi,NilaiUjian,NilaiEvaluasi,RekapDetailNilai};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Roles
        $rLaboran = Role::firstOrCreate(['role_name'=>'laboran']);
        $rAsisten = Role::firstOrCreate(['role_name'=>'asisten']);
        $rDosen   = Role::firstOrCreate(['role_name'=>'dosen']);

        // ── User untuk Laboran
        $uLaboran = User::firstOrCreate(['username'=>'laboran'],   ['password'=>Hash::make(env('PASSWORD_LABORAN')),'role_id'=>$rLaboran->id]);

        // ── Users untuk Asisten & Dosen
        $uAsisten1= User::firstOrCreate(['username'=>'asisten1'],  ['password'=>Hash::make('asisten123'),'role_id'=>$rAsisten->id]);
        $uAsisten2= User::firstOrCreate(['username'=>'asisten2'],  ['password'=>Hash::make('asisten123'),'role_id'=>$rAsisten->id]);
        $uDosen1  = User::firstOrCreate(['username'=>'dosen1'],    ['password'=>Hash::make('dosen123'),  'role_id'=>$rDosen->id]);

        // ── Ruangan
        $r1 = Ruangan::firstOrCreate(['nama_ruangan'=>'Lab StartUp']);
        $r2 = Ruangan::firstOrCreate(['nama_ruangan'=>'Lab IOT']);
        $r3 = Ruangan::firstOrCreate(['nama_ruangan'=>'Lab Computer Netwrok']);
        $r3 = Ruangan::firstOrCreate(['nama_ruangan'=>'Lab Computer Netwrok']);
        
        // ── Mata Kuliah
        $mkBD  = MataKuliah::firstOrCreate(['kode_mk'=>'IF-BD'],  ['nama_mk'=>'Basis Data']);
        $mkSO  = MataKuliah::firstOrCreate(['kode_mk'=>'IF-SO'],  ['nama_mk'=>'Sistem Operasi']);
        $mkJKO = MataKuliah::firstOrCreate(['kode_mk'=>'IF-JKO'], ['nama_mk'=>'Jaringan Komputer']);

        // ── Dosen (tanpa mata_kuliah_id — relasi via praktikum)
        $dosen1 = Dosen::firstOrCreate(['user_id'=>$uDosen1->id],
            ['nama_dosen'=>'Dr. Budi Santoso, M.T.','nidn'=>'0012345678']);

        // ── Asisten
        $asisten1 = Asisten::firstOrCreate(['user_id'=>$uAsisten1->id],
            ['nama_asisten'=>'Ahmad Fauzi','nim'=>'13020210001']);
        $asisten2 = Asisten::firstOrCreate(['user_id'=>$uAsisten2->id],
            ['nama_asisten'=>'Siti Rahayu','nim'=>'13020210002']);

        // ── Praktikum (kelas)
        $kelas1 = Praktikum::firstOrCreate(
            ['mata_kuliah_id'=>$mkBD->id,'nama_kelas'=>'Kelas A'],
            ['jadwal'=>'Senin 08:00–10:00','ruangan_id'=>$r1->id,'dosen_id'=>$dosen1->id,'asisten_id'=>$asisten1->id]
        );
        $kelas2 = Praktikum::firstOrCreate(
            ['mata_kuliah_id'=>$mkBD->id,'nama_kelas'=>'Kelas B'],
            ['jadwal'=>'Selasa 10:00–12:00','ruangan_id'=>$r2->id,'dosen_id'=>$dosen1->id,'asisten_id'=>$asisten2->id]
        );
        $kelas3 = Praktikum::firstOrCreate(
            ['mata_kuliah_id'=>$mkSO->id,'nama_kelas'=>'Kelas A'],
            ['jadwal'=>'Rabu 08:00–10:00','ruangan_id'=>$r1->id,'asisten_id'=>$asisten2->id]
        );

        // ── Mahasiswa (langsung ke kelas, bukan ke MK)
        $mhsList = [
            ['nim_mahasiswa'=>'13020220001','nama_mahasiswa'=>'Muhammad Rizky',    'praktikum_id'=>$kelas1->id],
            ['nim_mahasiswa'=>'13020220002','nama_mahasiswa'=>'Nurul Hidayah',     'praktikum_id'=>$kelas1->id],
            ['nim_mahasiswa'=>'13020220003','nama_mahasiswa'=>'Andi Pratama',      'praktikum_id'=>$kelas1->id],
            ['nim_mahasiswa'=>'13020220004','nama_mahasiswa'=>'Dewi Anggraini',    'praktikum_id'=>$kelas1->id],
            ['nim_mahasiswa'=>'13020220005','nama_mahasiswa'=>'Farhan Maulana',    'praktikum_id'=>$kelas1->id],
            ['nim_mahasiswa'=>'13020220006','nama_mahasiswa'=>'Reza Firmansyah',   'praktikum_id'=>$kelas2->id],
            ['nim_mahasiswa'=>'13020220007','nama_mahasiswa'=>'Indah Permatasari', 'praktikum_id'=>$kelas2->id],
            ['nim_mahasiswa'=>'13020220008','nama_mahasiswa'=>'Bagas Prasetyo',    'praktikum_id'=>$kelas3->id],
        ];
        $mhsObjs = [];
        foreach ($mhsList as $d) {
            $mhsObjs[] = Mahasiswa::firstOrCreate(['nim_mahasiswa'=>$d['nim_mahasiswa']], $d);
        }

        // ── Presensi untuk kelas1 (3 pertemuan demo)
        $kelas1Mhs = array_slice($mhsObjs, 0, 5);
        $pola = [1=>['H','H','H','H','H'], 2=>['H','A','H','H','I'], 3=>['H','H','S','H','H']];
        foreach ($kelas1Mhs as $i => $m) {
            foreach ($pola as $pertemuan => $statuses) {
                Presensi::firstOrCreate(
                    ['mahasiswa_id'=>$m->id,'praktikum_id'=>$kelas1->id,'pertemuan_ke'=>$pertemuan],
                    ['status_kehadiran'=>$statuses[$i],'catatan'=>null]
                );
            }
        }

        // ── Nilai untuk kelas1
        foreach ($kelas1Mhs as $m) {
            NilaiEvaluasi::firstOrCreate(['mahasiswa_id'=>$m->id,'praktikum_id'=>$kelas1->id],[
                'nilai_evaluasi1'=>rand(70,95),'nilai_evaluasi2'=>rand(70,95),
                'nilai_evaluasi3'=>rand(70,95),'nilai_evaluasi4'=>rand(70,95),
            ]);
            NilaiAsistensi::firstOrCreate(['mahasiswa_id'=>$m->id,'praktikum_id'=>$kelas1->id],[
                'nilai_asistensi1'=>rand(75,95),'nilai_asistensi2'=>rand(75,95),'nilai_asistensi3'=>rand(75,95),
            ]);
            NilaiUjian::firstOrCreate(['mahasiswa_id'=>$m->id,'praktikum_id'=>$kelas1->id],[
                'nilai_MID'=>rand(65,90),'nilai_UAS'=>rand(65,90),
            ]);
            RekapDetailNilai::hitungDanSimpan($m->id, $kelas1->id);
        }

        $this->command->info('✅ Seeder selesai!');
        $this->command->table(
            ['Role','Username','Password','Keterangan'],
            [
                ['Laboran','laboran','.env','Kelola semua data'],
                ['Asisten','asisten1','asisten123','Kelas A — Basis Data'],
                ['Asisten','asisten2','asisten123','Kelas B Basis Data + SO Kelas A'],
                ['Dosen','dosen1','dosen123','Monitor Basis Data Kelas A & B'],
            ]
        );
    }
}
