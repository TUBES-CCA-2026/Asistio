<?php
namespace Database\Seeders;
use App\Models\{Role,User,MataKuliah,Ruangan,Dosen,Asisten,Praktikum,Mahasiswa,Presensi,NilaiAsistensi,NilaiUjian,NilaiEvaluasi,RekapDetailNilai};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ══════════════════════════════════════════════════════════════════
        // ROLES
        // ══════════════════════════════════════════════════════════════════
        $rLaboran = Role::firstOrCreate(['role_name' => 'laboran']);
        $rAsisten = Role::firstOrCreate(['role_name' => 'asisten']);
        $rDosen   = Role::firstOrCreate(['role_name' => 'dosen']);

        // ══════════════════════════════════════════════════════════════════
        // USER LABORAN
        // ══════════════════════════════════════════════════════════════════
        User::firstOrCreate(['username' => 'laboran'], [
            'password' => Hash::make(env('PASSWORD_LABORAN', 'laboran123')),
            'role_id'  => $rLaboran->id,
        ]);

        // ══════════════════════════════════════════════════════════════════
        // USER & ASISTEN (24 asisten)
        // ══════════════════════════════════════════════════════════════════
        $asistenData = [
            ['username' => 'asisten1',  'password' => 'asisten123', 'nama' => 'Ahmad Fauzi',                   'nim' => '13020210001'],
            ['username' => 'asisten2',  'password' => 'asisten123', 'nama' => 'Siti Rahayu',                   'nim' => '13020210002'],
            ['username' => 'bintang',   'password' => 'password',   'nama' => 'Arya Bintang Kusuma Wijaya',    'nim' => '13020210003'],
            ['username' => 'agys',      'password' => 'password',   'nama' => 'Ghiffary Agys Al Baihaqy',      'nim' => '13020210004'],
            ['username' => 'valdi',     'password' => 'password',   'nama' => 'M Rivaldi Juliadin',            'nim' => '13020210005'],
            ['username' => 'fahmi',     'password' => 'password',   'nama' => 'Muh Fahmi Ashar',               'nim' => '13020210006'],
            ['username' => 'rayhan',    'password' => 'password',   'nama' => 'Rayhan Firrizqi',               'nim' => '13020210007'],
            ['username' => 'rendi',     'password' => 'password',   'nama' => 'Rendi Pratama',                 'nim' => '13020210008'],
            ['username' => 'nabil',     'password' => 'password',   'nama' => 'Muhammad Nabil Bassalam',       'nim' => '13020210009'],
            ['username' => 'ariq',      'password' => 'password',   'nama' => 'Saefullah Ahmad Ariiq Sr',      'nim' => '13020210010'],
            ['username' => 'nendra',    'password' => 'password',   'nama' => 'Nendra Rizkullah Izzatul Ibad', 'nim' => '13020210011'],
            ['username' => 'rahmat',    'password' => 'password',   'nama' => 'Rahmat Setiawan Rahman',        'nim' => '13020210012'],
            ['username' => 'fadyl',     'password' => 'password',   'nama' => 'Ahmad Fadyl Sapri',             'nim' => '13020210013'],
            ['username' => 'saad',      'password' => 'password',   'nama' => "Muhammad Sa'ad Wahid",          'nim' => '13020210014'],
            ['username' => 'karima',    'password' => 'password',   'nama' => 'Karima',                        'nim' => '13020210015'],
            ['username' => 'mekar',     'password' => 'password',   'nama' => 'Mekar Wangi R',                 'nim' => '13020210016'],
            ['username' => 'nurul',     'password' => 'password',   'nama' => 'Nurul Aulia Badawi',            'nim' => '13020210017'],
            ['username' => 'qamri',     'password' => 'password',   'nama' => 'Nurul Qamri Ramadhina',         'nim' => '13020210018'],
            ['username' => 'rahma',     'password' => 'password',   'nama' => 'Rahmawati',                     'nim' => '13020210019'],
            ['username' => 'fadia',     'password' => 'password',   'nama' => 'Fadia Syakinah Amalia',         'nim' => '13020210020'],
            ['username' => 'alisa',     'password' => 'password',   'nama' => 'Nur Alisa',                     'nim' => '13020210021'],
            ['username' => 'tiara',     'password' => 'password',   'nama' => 'Tiara Mulya Pratiwi',           'nim' => '13020210022'],
            ['username' => 'najiya',    'password' => 'password',   'nama' => 'Najiya N Ngabito',              'nim' => '13020210023'],
            ['username' => 'kharisma',  'password' => 'password',   'nama' => 'Kharisma Suchy Aisyah',         'nim' => '13020210024'],
        ];

        $asisten = [];
        foreach ($asistenData as $d) {
            $user = User::firstOrCreate(['username' => $d['username']], [
                'password' => Hash::make($d['password']),
                'role_id'  => $rAsisten->id,
            ]);
            $asisten[$d['username']] = Asisten::firstOrCreate(['user_id' => $user->id], [
                'nama_asisten' => $d['nama'],
                'nim'          => $d['nim'],
            ]);
        }

        // ══════════════════════════════════════════════════════════════════
        // USER & DOSEN (5 dosen)
        // ══════════════════════════════════════════════════════════════════
        $dosenData = [
            ['username' => 'dosen1', 'password' => 'dosen123', 'nama' => 'Dr. Budi Santoso, M.T.',      'nidn' => '0012345678'],
            ['username' => 'dosen2', 'password' => 'dosen123', 'nama' => 'Dr. Rina Wati, M.Kom.',       'nidn' => '0023456789'],
            ['username' => 'dosen3', 'password' => 'dosen123', 'nama' => 'Dr. Hendra Wijaya, M.T.',     'nidn' => '0034567890'],
            ['username' => 'dosen4', 'password' => 'dosen123', 'nama' => 'Dr. Sari Dewi, M.Kom.',       'nidn' => '0045678901'],
            ['username' => 'dosen5', 'password' => 'dosen123', 'nama' => 'Dr. Agus Purnomo, M.T.',      'nidn' => '0056789012'],
        ];

        $dosen = [];
        foreach ($dosenData as $d) {
            $user = User::firstOrCreate(['username' => $d['username']], [
                'password' => Hash::make($d['password']),
                'role_id'  => $rDosen->id,
            ]);
            $dosen[$d['username']] = Dosen::firstOrCreate(['user_id' => $user->id], [
                'nama_dosen' => $d['nama'],
                'nidn'       => $d['nidn'],
            ]);
        }

        // ══════════════════════════════════════════════════════════════════
        // RUANGAN (7 lab)
        // ══════════════════════════════════════════════════════════════════
        $ruangan = [];
        foreach ([
            'Lab StartUp', 'Lab IOT', 'Lab Computer Network',
            'Lab Multimedia', 'Lab Computer Vision',
            'Lab Data Science', 'Lab Microcontroller',
        ] as $nama) {
            $ruangan[] = Ruangan::firstOrCreate(['nama_ruangan' => $nama]);
        }
        [$r1,$r2,$r3,$r4,$r5,$r6,$r7] = $ruangan;

        // ══════════════════════════════════════════════════════════════════
        // MATA KULIAH (8 MK)
        // ══════════════════════════════════════════════════════════════════
        $mkBD    = MataKuliah::firstOrCreate(['kode_mk' => 'IF-BD'],    ['nama_mk' => 'Basis Data']);
        $mkBD2   = MataKuliah::firstOrCreate(['kode_mk' => 'IF-BD2'],   ['nama_mk' => 'Basis Data 2']);
        $mkJKO   = MataKuliah::firstOrCreate(['kode_mk' => 'IF-JKO'],   ['nama_mk' => 'Jaringan Komputer']);
        $mkPW    = MataKuliah::firstOrCreate(['kode_mk' => 'IF-PW'],    ['nama_mk' => 'Pemrograman Web']);
        $mkPBO   = MataKuliah::firstOrCreate(['kode_mk' => 'IF-PBO'],   ['nama_mk' => 'Pemrograman Berorientasi Objek']);
        $mkSTR   = MataKuliah::firstOrCreate(['kode_mk' => 'IF-STR'],   ['nama_mk' => 'Struktur Data']);
        $mkALPRO = MataKuliah::firstOrCreate(['kode_mk' => 'IF-ALPRO'], ['nama_mk' => 'Algoritma dan Pemrograman']);
        $mkELDAS = MataKuliah::firstOrCreate(['kode_mk' => 'IF-ELDAS'], ['nama_mk' => 'Elektronika Dasar']);

        // ══════════════════════════════════════════════════════════════════
        // KELAS PRAKTIKUM
        // Struktur: [mk, nama_kelas, jadwal, ruangan, dosen, asisten1, asisten2|null]
        //
        // Total 16 kelas → 24 asisten terdistribusi:
        //   - 16 sebagai Asisten 1 (tiap kelas wajib punya)
        //   - 8  sebagai Asisten 2  (kelas padat/besar)
        //   → semua 24 asisten kebagian minimal 1 kelas
        // ══════════════════════════════════════════════════════════════════
        $a = $asisten; // alias pendek
        $d = $dosen;

        $kelasConfig = [
            // ── Basis Data (4 kelas: A1-A4) — Dosen1
            ['mk'=>$mkBD,  'nama'=>'A1','hari'=>'Senin',  'mulai'=>'07:00','selesai'=>'09:30', 'ruangan'=>$r1,'dosen'=>$d['dosen1'],'ast1'=>$a['asisten1'], 'ast2'=>$a['bintang']],
            ['mk'=>$mkBD,  'nama'=>'A2','hari'=>'Senin',  'mulai'=>'09:40','selesai'=>'12:10', 'ruangan'=>$r2,'dosen'=>$d['dosen1'],'ast1'=>$a['asisten2'], 'ast2'=>$a['agys']],
            ['mk'=>$mkBD,  'nama'=>'A3','hari'=>'Selasa', 'mulai'=>'07:00','selesai'=>'09:30', 'ruangan'=>$r3,'dosen'=>$d['dosen1'],'ast1'=>$a['valdi'],    'ast2'=>null],
            ['mk'=>$mkBD,  'nama'=>'A4','hari'=>'Selasa', 'mulai'=>'09:40','selesai'=>'12:10', 'ruangan'=>$r4,'dosen'=>$d['dosen1'],'ast1'=>$a['fahmi'],    'ast2'=>null],

            // ── Basis Data 2 (2 kelas: B1-B2) — Dosen2
            ['mk'=>$mkBD2, 'nama'=>'B1','hari'=>'Rabu',   'mulai'=>'07:00','selesai'=>'09:30', 'ruangan'=>$r1,'dosen'=>$d['dosen2'],'ast1'=>$a['rayhan'],   'ast2'=>$a['rendi']],
            ['mk'=>$mkBD2, 'nama'=>'B2','hari'=>'Rabu',   'mulai'=>'09:40','selesai'=>'12:10', 'ruangan'=>$r2,'dosen'=>$d['dosen2'],'ast1'=>$a['nabil'],    'ast2'=>null],

            // ── Jaringan Komputer (2 kelas: A1-A2) — Dosen3
            ['mk'=>$mkJKO, 'nama'=>'A1','hari'=>'Kamis',  'mulai'=>'07:00','selesai'=>'09:30', 'ruangan'=>$r3,'dosen'=>$d['dosen3'],'ast1'=>$a['ariq'],     'ast2'=>$a['nendra']],
            ['mk'=>$mkJKO, 'nama'=>'A2','hari'=>'Kamis',  'mulai'=>'09:40','selesai'=>'12:10', 'ruangan'=>$r7,'dosen'=>$d['dosen3'],'ast1'=>$a['rahmat'],   'ast2'=>null],

            // ── Pemrograman Web (2 kelas: A1-A2) — Dosen2
            ['mk'=>$mkPW,  'nama'=>'A1','hari'=>'Jumat',  'mulai'=>'07:00','selesai'=>'09:30', 'ruangan'=>$r5,'dosen'=>$d['dosen2'],'ast1'=>$a['fadyl'],    'ast2'=>$a['saad']],
            ['mk'=>$mkPW,  'nama'=>'A2','hari'=>'Jumat',  'mulai'=>'09:40','selesai'=>'12:10', 'ruangan'=>$r6,'dosen'=>$d['dosen2'],'ast1'=>$a['karima'],   'ast2'=>null],

            // ── PBO (2 kelas: A1-A2) — Dosen4
            ['mk'=>$mkPBO, 'nama'=>'A1','hari'=>'Senin',  'mulai'=>'13:00','selesai'=>'15:30', 'ruangan'=>$r4,'dosen'=>$d['dosen4'],'ast1'=>$a['mekar'],    'ast2'=>$a['nurul']],
            ['mk'=>$mkPBO, 'nama'=>'A2','hari'=>'Selasa', 'mulai'=>'13:00','selesai'=>'15:30', 'ruangan'=>$r5,'dosen'=>$d['dosen4'],'ast1'=>$a['qamri'],    'ast2'=>null],

            // ── Struktur Data (2 kelas: A1-A2) — Dosen5
            ['mk'=>$mkSTR, 'nama'=>'A1','hari'=>'Rabu',   'mulai'=>'13:00','selesai'=>'15:30', 'ruangan'=>$r6,'dosen'=>$d['dosen5'],'ast1'=>$a['rahma'],    'ast2'=>$a['fadia']],
            ['mk'=>$mkSTR, 'nama'=>'A2','hari'=>'Kamis',  'mulai'=>'13:00','selesai'=>'15:30', 'ruangan'=>$r7,'dosen'=>$d['dosen5'],'ast1'=>$a['alisa'],    'ast2'=>null],

            // ── Algoritma & Pemrograman (1 kelas: A1) — Dosen4
            ['mk'=>$mkALPRO,'nama'=>'A1','hari'=>'Jumat', 'mulai'=>'13:00','selesai'=>'15:30', 'ruangan'=>$r1,'dosen'=>$d['dosen4'],'ast1'=>$a['tiara'],    'ast2'=>$a['najiya']],

            // ── Elektronika Dasar (1 kelas: A1) — Dosen3
            ['mk'=>$mkELDAS,'nama'=>'A1','hari'=>'Senin', 'mulai'=>'15:40','selesai'=>'18:10', 'ruangan'=>$r7,'dosen'=>$d['dosen3'],'ast1'=>$a['kharisma'],'ast2'=>null],
        ];

        $kelasList = [];
        foreach ($kelasConfig as $cfg) {
            $kelas = Praktikum::firstOrCreate(
                ['mata_kuliah_id' => $cfg['mk']->id, 'nama_kelas' => $cfg['nama']],
                [
                    'hari'        => $cfg['hari'],
                    'jam_mulai'   => $cfg['mulai'],
                    'jam_selesai' => $cfg['selesai'],
                    'jadwal'      => $cfg['hari'] . ', ' . $cfg['mulai'] . '–' . $cfg['selesai'],
                    'ruangan_id'  => $cfg['ruangan']->id,
                    'dosen_id'    => $cfg['dosen']->id,
                    'asisten_id'  => $cfg['ast1']->id,
                    'asisten2_id' => $cfg['ast2']?->id,
                ]
            );
            $kelasList[] = $kelas;
        }

        // ══════════════════════════════════════════════════════════════════
        // MAHASISWA (40 mahasiswa)
        // ══════════════════════════════════════════════════════════════════
        $mhsData = [
            ['nim'=>'13020220001','nama'=>'Muhammad Rizky'],
            ['nim'=>'13020220002','nama'=>'Nurul Hidayah'],
            ['nim'=>'13020220003','nama'=>'Andi Pratama'],
            ['nim'=>'13020220004','nama'=>'Dewi Anggraini'],
            ['nim'=>'13020220005','nama'=>'Farhan Maulana'],
            ['nim'=>'13020220006','nama'=>'Reza Firmansyah'],
            ['nim'=>'13020220007','nama'=>'Indah Permatasari'],
            ['nim'=>'13020220008','nama'=>'Bagas Prasetyo'],
            ['nim'=>'13020220009','nama'=>'Ayu Lestari'],
            ['nim'=>'13020220010','nama'=>'Dimas Kurniawan'],
            ['nim'=>'13020220011','nama'=>'Fitri Handayani'],
            ['nim'=>'13020220012','nama'=>'Gilang Ramadan'],
            ['nim'=>'13020220013','nama'=>'Hana Safira'],
            ['nim'=>'13020220014','nama'=>'Ivan Setiawan'],
            ['nim'=>'13020220015','nama'=>'Julia Pratiwi'],
            ['nim'=>'13020220016','nama'=>'Kevin Aditya'],
            ['nim'=>'13020220017','nama'=>'Lina Marlina'],
            ['nim'=>'13020220018','nama'=>'Mario Susanto'],
            ['nim'=>'13020220019','nama'=>'Nina Rahayu'],
            ['nim'=>'13020220020','nama'=>'Oscar Wijaya'],
            ['nim'=>'13020220021','nama'=>'Putri Amalia'],
            ['nim'=>'13020220022','nama'=>'Qori Ananda'],
            ['nim'=>'13020220023','nama'=>'Rizal Hakim'],
            ['nim'=>'13020220024','nama'=>'Salsabila Nur'],
            ['nim'=>'13020220025','nama'=>'Taufik Hidayat'],
            ['nim'=>'13020220026','nama'=>'Umi Kalsum'],
            ['nim'=>'13020220027','nama'=>'Vina Permata'],
            ['nim'=>'13020220028','nama'=>'Wahyu Santoso'],
            ['nim'=>'13020220029','nama'=>'Xena Pratiwi'],
            ['nim'=>'13020220030','nama'=>'Yusuf Effendi'],
            ['nim'=>'13020220031','nama'=>'Zahra Aulia'],
            ['nim'=>'13020220032','nama'=>'Arif Budiman'],
            ['nim'=>'13020220033','nama'=>'Bella Safitri'],
            ['nim'=>'13020220034','nama'=>'Candra Putra'],
            ['nim'=>'13020220035','nama'=>'Dian Novita'],
            ['nim'=>'13020220036','nama'=>'Eko Prasetyo'],
            ['nim'=>'13020220037','nama'=>'Fira Salsabila'],
            ['nim'=>'13020220038','nama'=>'Galih Wicaksono'],
            ['nim'=>'13020220039','nama'=>'Hesti Wulandari'],
            ['nim'=>'13020220040','nama'=>'Ilham Saputra'],
        ];

        $mhs = [];
        foreach ($mhsData as $d) {
            $mhs[] = Mahasiswa::firstOrCreate(
                ['nim_mahasiswa' => $d['nim']],
                ['nama_mahasiswa' => $d['nama']]
            );
        }

        // ══════════════════════════════════════════════════════════════════
        // DISTRIBUSI MAHASISWA KE KELAS
        // Tiap kelas dapat 5-8 mahasiswa (beberapa mahasiswa ikut 2 MK)
        // ══════════════════════════════════════════════════════════════════
        //                          idx mahasiswa (0-based)
        $distribusi = [
            0  => [0,1,2,3,4,5,6],          // BD A1        (7 mhs)
            1  => [7,8,9,10,11,12],          // BD A2        (6 mhs)
            2  => [13,14,15,16,17],          // BD A3        (5 mhs)
            3  => [18,19,20,21,22,23],       // BD A4        (6 mhs)
            4  => [24,25,26,27,28],          // BD2 B1       (5 mhs)
            5  => [29,30,31,32,33],          // BD2 B2       (5 mhs)
            6  => [0,5,10,15,20,25,34],      // JKO A1       (7 mhs, lintas kelas)
            7  => [1,6,11,16,21,26,35],      // JKO A2       (7 mhs)
            8  => [2,7,12,17,22,27,36],      // PW  A1       (7 mhs)
            9  => [3,8,13,18,23,28,37],      // PW  A2       (7 mhs)
            10 => [4,9,14,19,24,29,38],      // PBO A1       (7 mhs)
            11 => [30,31,32,33,34,35],       // PBO A2       (6 mhs)
            12 => [36,37,38,39,0,1],         // STR A1       (6 mhs)
            13 => [2,3,4,5,6,7],             // STR A2       (6 mhs)
            14 => [8,9,10,11,12,13,14],      // ALPRO A1     (7 mhs)
            15 => [15,16,17,18,19,20],       // ELDAS A1     (6 mhs)
        ];

        foreach ($distribusi as $kelasIdx => $mhsIdxList) {
            foreach ($mhsIdxList as $mi) {
                $kelasList[$kelasIdx]->mahasiswa()->syncWithoutDetaching([$mhs[$mi]->id]);
            }
        }

        // ══════════════════════════════════════════════════════════════════
        // PRESENSI DEMO (6 pertemuan untuk semua kelas)
        // Status: H=Hadir, A=Alpa, I=Izin, S=Sakit
        // ══════════════════════════════════════════════════════════════════
        // Pola status untuk tiap pertemuan — dirotasi per mahasiswa
        $statusPool = ['H','H','H','H','H','H','I','S','A','H'];

        foreach ($kelasList as $kelas) {
            $anggota = $kelas->mahasiswa()->get();
            foreach ($anggota as $idx => $m) {
                for ($pertemuan = 1; $pertemuan <= 6; $pertemuan++) {
                    // Variasi status berdasarkan index mahasiswa & pertemuan
                    $statusIdx = ($idx + $pertemuan) % count($statusPool);
                    // Mahasiswa ke-3 di setiap kelas dibuat punya banyak alpa (demo warning)
                    if ($idx === 2 && $pertemuan >= 2) {
                        $status = 'A';
                    } else {
                        $status = $statusPool[$statusIdx];
                    }
                    Presensi::firstOrCreate(
                        ['mahasiswa_id' => $m->id, 'praktikum_id' => $kelas->id, 'pertemuan_ke' => $pertemuan],
                        ['status_kehadiran' => $status, 'catatan' => null]
                    );
                }
            }
        }

        // ══════════════════════════════════════════════════════════════════
        // NILAI DEMO (untuk semua kelas & mahasiswa)
        // ══════════════════════════════════════════════════════════════════
        foreach ($kelasList as $kelas) {
            $anggota = $kelas->mahasiswa()->get();
            foreach ($anggota as $idx => $m) {
                // Mahasiswa ke-3 (demo alpa) beri nilai rendah
                $min = ($idx === 2) ? 40 : 65;
                $max = ($idx === 2) ? 65 : 95;

                NilaiEvaluasi::firstOrCreate(
                    ['mahasiswa_id' => $m->id, 'praktikum_id' => $kelas->id],
                    [
                        'p1' =>rand($min,$max),'p2' =>rand($min,$max),'p3' =>rand($min,$max),
                        'p4' =>rand($min,$max),'p5' =>rand($min,$max),'p6' =>rand($min,$max),
                        'p7' =>rand($min,$max),'p8' =>rand($min,$max),'p9' =>rand($min,$max),
                        'p10'=>rand($min,$max),'p11'=>rand($min,$max),'p12'=>rand($min,$max),
                        'p13'=>rand($min,$max),'p14'=>rand($min,$max),
                    ]
                );
                NilaiAsistensi::firstOrCreate(
                    ['mahasiswa_id' => $m->id, 'praktikum_id' => $kelas->id],
                    [
                        'nilai_asistensi1' => rand($min,$max),
                        'nilai_asistensi2' => rand($min,$max),
                        'nilai_asistensi3' => rand($min,$max),
                    ]
                );
                NilaiUjian::firstOrCreate(
                    ['mahasiswa_id' => $m->id, 'praktikum_id' => $kelas->id],
                    [
                        'nilai_MID' => rand($min,$max),
                        'nilai_UAS' => rand($min,$max),
                    ]
                );
                RekapDetailNilai::hitungDanSimpan($m->id, $kelas->id);
            }
        }

        // ══════════════════════════════════════════════════════════════════
        // SUMMARY TABLE
        // ══════════════════════════════════════════════════════════════════
        $this->command->info('');
        $this->command->info('✅ Seeder selesai! Ringkasan akun:');
        $this->command->table(
            ['Role', 'Username', 'Password', 'Kelas yang Diampu'],
            [
                ['Laboran',  'laboran',   '.env / laboran123', 'Semua data'],
                ['Dosen',    'dosen1',    'dosen123',          'BD A1, A2, A3, A4'],
                ['Dosen',    'dosen2',    'dosen123',          'BD2 B1, B2 + PW A1, A2'],
                ['Dosen',    'dosen3',    'dosen123',          'JKO A1, A2 + ELDAS A1'],
                ['Dosen',    'dosen4',    'dosen123',          'PBO A1, A2 + ALPRO A1'],
                ['Dosen',    'dosen5',    'dosen123',          'STR A1, A2'],
                ['Asisten',  'asisten1',  'asisten123',        'BD A1 (Asisten 1)'],
                ['Asisten',  'asisten2',  'asisten123',        'BD A2 (Asisten 1)'],
                ['Asisten',  'bintang',   'password',          'BD A1 (Asisten 2)'],
                ['Asisten',  'agys',      'password',          'BD A2 (Asisten 2)'],
                ['Asisten',  'valdi',     'password',          'BD A3 (Asisten 1)'],
                ['Asisten',  'fahmi',     'password',          'BD A4 (Asisten 1)'],
                ['Asisten',  'rayhan',    'password',          'BD2 B1 (Asisten 1)'],
                ['Asisten',  'rendi',     'password',          'BD2 B1 (Asisten 2)'],
                ['Asisten',  'nabil',     'password',          'BD2 B2 (Asisten 1)'],
                ['Asisten',  'ariq',      'password',          'JKO A1 (Asisten 1)'],
                ['Asisten',  'nendra',    'password',          'JKO A1 (Asisten 2)'],
                ['Asisten',  'rahmat',    'password',          'JKO A2 (Asisten 1)'],
                ['Asisten',  'fadyl',     'password',          'PW A1 (Asisten 1)'],
                ['Asisten',  'saad',      'password',          'PW A1 (Asisten 2)'],
                ['Asisten',  'karima',    'password',          'PW A2 (Asisten 1)'],
                ['Asisten',  'mekar',     'password',          'PBO A1 (Asisten 1)'],
                ['Asisten',  'nurul',     'password',          'PBO A1 (Asisten 2)'],
                ['Asisten',  'qamri',     'password',          'PBO A2 (Asisten 1)'],
                ['Asisten',  'rahma',     'password',          'STR A1 (Asisten 1)'],
                ['Asisten',  'fadia',     'password',          'STR A1 (Asisten 2)'],
                ['Asisten',  'alisa',     'password',          'STR A2 (Asisten 1)'],
                ['Asisten',  'tiara',     'password',          'ALPRO A1 (Asisten 1)'],
                ['Asisten',  'najiya',    'password',          'ALPRO A1 (Asisten 2)'],
                ['Asisten',  'kharisma',  'password',          'ELDAS A1 (Asisten 1)'],
            ]
        );
    }
}
