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

        // ── Users untuk Asisten
        $uAsisten1= User::firstOrCreate(['username'=>'asisten1'],  ['password'=>Hash::make('asisten123'),'role_id'=>$rAsisten->id]);
        $uAsisten2= User::firstOrCreate(['username'=>'asisten2'],  ['password'=>Hash::make('asisten123'),'role_id'=>$rAsisten->id]);
        $uBintang = User::firstOrCreate(['username'=>'bintang'],   ['password'=>Hash::make('password'),'role_id'=>$rAsisten->id]);
        $uAgys    = User::firstOrCreate(['username'=>'agys'],      ['password'=>Hash::make('password'),'role_id'=>$rAsisten->id]);
        $uValdi   = User::firstOrCreate(['username'=>'valdi'],     ['password'=>Hash::make('password'),'role_id'=>$rAsisten->id]);
        $uFahmi   = User::firstOrCreate(['username'=>'fahmi'],     ['password'=>Hash::make('password'),'role_id'=>$rAsisten->id]);
        $uRayhan  = User::firstOrCreate(['username'=>'rayhan'],    ['password'=>Hash::make('password'),'role_id'=>$rAsisten->id]);
        $uRendi   = User::firstOrCreate(['username'=>'rendi'],     ['password'=>Hash::make('password'),'role_id'=>$rAsisten->id]);
        $uNabil   = User::firstOrCreate(['username'=>'nabil'],     ['password'=>Hash::make('password'),'role_id'=>$rAsisten->id]);
        $uAriq    = User::firstOrCreate(['username'=>'ariq'],      ['password'=>Hash::make('password'),'role_id'=>$rAsisten->id]);
        $uNendra  = User::firstOrCreate(['username'=>'nendra'],    ['password'=>Hash::make('password'),'role_id'=>$rAsisten->id]);
        $uRahmat  = User::firstOrCreate(['username'=>'rahmat'],    ['password'=>Hash::make('password'),'role_id'=>$rAsisten->id]);
        $uFadyl   = User::firstOrCreate(['username'=>'fadyl'],     ['password'=>Hash::make('password'),'role_id'=>$rAsisten->id]);
        $uSaad    = User::firstOrCreate(['username'=>'saad'],      ['password'=>Hash::make('password'),'role_id'=>$rAsisten->id]);
        $uKarima  = User::firstOrCreate(['username'=>'karima'],    ['password'=>Hash::make('password'),'role_id'=>$rAsisten->id]);
        $uMekar   = User::firstOrCreate(['username'=>'mekar'],     ['password'=>Hash::make('password'),'role_id'=>$rAsisten->id]);
        $uNurul   = User::firstOrCreate(['username'=>'nurul'],     ['password'=>Hash::make('password'),'role_id'=>$rAsisten->id]);
        $uQamri   = User::firstOrCreate(['username'=>'qamri'],     ['password'=>Hash::make('password'),'role_id'=>$rAsisten->id]);
        $uRahma   = User::firstOrCreate(['username'=>'rahma'],     ['password'=>Hash::make('password'),'role_id'=>$rAsisten->id]);
        $uFadia   = User::firstOrCreate(['username'=>'fadia'],     ['password'=>Hash::make('password'),'role_id'=>$rAsisten->id]);
        $uAlisa   = User::firstOrCreate(['username'=>'alisa'],     ['password'=>Hash::make('password'),'role_id'=>$rAsisten->id]);
        $uTiara   = User::firstOrCreate(['username'=>'tiara'],     ['password'=>Hash::make('password'),'role_id'=>$rAsisten->id]);
        $uNajiya  = User::firstOrCreate(['username'=>'najiya'],    ['password'=>Hash::make('password'),'role_id'=>$rAsisten->id]);
        $uKharisma= User::firstOrCreate(['username'=>'kharisma'],  ['password'=>Hash::make('password'),'role_id'=>$rAsisten->id]);

        // ── User untuk Dosen
        $uDosen1  = User::firstOrCreate(['username'=>'dosen1'],    ['password'=>Hash::make('dosen123'),  'role_id'=>$rDosen->id]);

        // ── Ruangan
        $r1 = Ruangan::firstOrCreate(['nama_ruangan'=>'Lab StartUp']);
        $r2 = Ruangan::firstOrCreate(['nama_ruangan'=>'Lab IOT']);
        $r3 = Ruangan::firstOrCreate(['nama_ruangan'=>'Lab Computer Netwrok']);
        $r4 = Ruangan::firstOrCreate(['nama_ruangan'=>'Lab Multimedia']);
        $r5 = Ruangan::firstOrCreate(['nama_ruangan'=>'Lab Computer Vision']);
        $r6 = Ruangan::firstOrCreate(['nama_ruangan'=>'Lab Data Science']);
        $r7 = Ruangan::firstOrCreate(['nama_ruangan'=>'Lab Microcontroller']);
        
        // ── Mata Kuliah
        $mkBD  = MataKuliah::firstOrCreate(['kode_mk'=>'IF-BD'],  ['nama_mk'=>'Basis Data']);
        $mkSO  = MataKuliah::firstOrCreate(['kode_mk'=>'IF-SO'],  ['nama_mk'=>'Sistem Operasi']);
        $mkJKO = MataKuliah::firstOrCreate(['kode_mk'=>'IF-JKO'], ['nama_mk'=>'Jaringan Komputer']);

        // ── Dosen (tanpa mata_kuliah_id — relasi via praktikum)
        $dosen1 = Dosen::firstOrCreate(['user_id'=>$uDosen1->id],
            ['nama_dosen'=>'Dr. Budi Santoso, M.T.','nidn'=>'0012345678']);

        // ── Asisten
        $asisten1        = Asisten::firstOrCreate(['user_id'=>$uAsisten1->id], ['nama_asisten'=>'Ahmad Fauzi','nim'=>'13020210001']);
        $asisten2        = Asisten::firstOrCreate(['user_id'=>$uAsisten2->id], ['nama_asisten'=>'Siti Rahayu','nim'=>'13020210002']);        
        $asistenBintang  = Asisten::firstOrCreate(['user_id'=>$uBintang->id],  ['nama_asisten'=>'Bintang', 'nim'=>'13020210003']);
        $asistenAgys     = Asisten::firstOrCreate(['user_id'=>$uAgys->id],     ['nama_asisten'=>'Agys',    'nim'=>'13020210004']);
        $asistenValdi    = Asisten::firstOrCreate(['user_id'=>$uValdi->id],    ['nama_asisten'=>'Valdi',   'nim'=>'13020210005']);
        $asistenFahmi    = Asisten::firstOrCreate(['user_id'=>$uFahmi->id],    ['nama_asisten'=>'Fahmi',   'nim'=>'13020210006']);
        $asistenRayhan   = Asisten::firstOrCreate(['user_id'=>$uRayhan->id],   ['nama_asisten'=>'Rayhan',  'nim'=>'13020210007']);
        $asistenRendi    = Asisten::firstOrCreate(['user_id'=>$uRendi->id],    ['nama_asisten'=>'Rendi',   'nim'=>'13020210008']);
        $asistenNabil    = Asisten::firstOrCreate(['user_id'=>$uNabil->id],    ['nama_asisten'=>'Nabil',   'nim'=>'13020210009']);
        $asistenAriq     = Asisten::firstOrCreate(['user_id'=>$uAriq->id],     ['nama_asisten'=>'Ariq',    'nim'=>'13020210010']);
        $asistenNendra   = Asisten::firstOrCreate(['user_id'=>$uNendra->id],   ['nama_asisten'=>'Nendra',  'nim'=>'13020210011']);
        $asistenRahmat   = Asisten::firstOrCreate(['user_id'=>$uRahmat->id],   ['nama_asisten'=>'Rahmat',  'nim'=>'13020210012']);
        $asistenFadyl    = Asisten::firstOrCreate(['user_id'=>$uFadyl->id],    ['nama_asisten'=>'Fadyl',   'nim'=>'13020210013']);
        $asistenSaad     = Asisten::firstOrCreate(['user_id'=>$uSaad->id],     ['nama_asisten'=>'Saad',    'nim'=>'13020210014']);
        $asistenKarima   = Asisten::firstOrCreate(['user_id'=>$uKarima->id],   ['nama_asisten'=>'Karima',  'nim'=>'13020210015']);
        $asistenMekar    = Asisten::firstOrCreate(['user_id'=>$uMekar->id],    ['nama_asisten'=>'Mekar',   'nim'=>'13020210016']);
        $asistenNurul    = Asisten::firstOrCreate(['user_id'=>$uNurul->id],    ['nama_asisten'=>'Nurul',   'nim'=>'13020210017']);
        $asistenQamri    = Asisten::firstOrCreate(['user_id'=>$uQamri->id],    ['nama_asisten'=>'Qamri',   'nim'=>'13020210018']);
        $asistenRahma    = Asisten::firstOrCreate(['user_id'=>$uRahma->id],    ['nama_asisten'=>'Rahma',   'nim'=>'13020210019']);
        $asistenFadia    = Asisten::firstOrCreate(['user_id'=>$uFadia->id],    ['nama_asisten'=>'Fadia',   'nim'=>'13020210020']);
        $asistenAlisa    = Asisten::firstOrCreate(['user_id'=>$uAlisa->id],    ['nama_asisten'=>'Alisa',   'nim'=>'13020210021']);
        $asistenTiara    = Asisten::firstOrCreate(['user_id'=>$uTiara->id],    ['nama_asisten'=>'Tiara',   'nim'=>'13020210022']);
        $asistenNajiya   = Asisten::firstOrCreate(['user_id'=>$uNajiya->id],   ['nama_asisten'=>'Najiya',  'nim'=>'13020210023']);
        $asistenKharisma = Asisten::firstOrCreate(['user_id'=>$uKharisma->id], ['nama_asisten'=>'Kharisma','nim'=>'13020210024']);    

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
                ['Asisten','bintang','password','-'],
                ['Asisten','agys','password','-'],
                ['Asisten','valdi','password','-'],
                ['Asisten','fahmi','password','-'],
                ['Asisten','rayhan','password','-'],
                ['Asisten','rendi','password','-'],
                ['Asisten','nabil','password','-'],
                ['Asisten','ariq','password','-'],
                ['Asisten','nendra','password','-'],
                ['Asisten','rahmat','password','-'],
                ['Asisten','fadyl','password','-'],
                ['Asisten','saad','password','-'],
                ['Asisten','karima','password','-'],
                ['Asisten','mekar','password','-'],
                ['Asisten','nurul','password','-'],
                ['Asisten','qamri','password','-'],
                ['Asisten','rahma','password','-'],
                ['Asisten','fadia','password','-'],
                ['Asisten','alisa','password','-'],
                ['Asisten','tiara','password','-'],
                ['Asisten','najiya','password','-'],
                ['Asisten','kharisma','password','-'],
                ['Dosen','dosen1','dosen123','Monitor Basis Data Kelas A & B'],
            ]
        );
    }
}
