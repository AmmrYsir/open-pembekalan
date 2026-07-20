<?php

namespace Database\Seeders;

use App\Models\MofCategory;
use App\Models\MofCode;
use App\Models\MofSubcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MofCodeSeeder extends Seeder
{
    /**
     * @var array<int, array{code: string, name: string, subcategories: array<int, array{code: string, name: string, mof_codes: array<int, array{code: string, name: string}>}>}>
     */
    private array $mof_codes = [
        [
            'code' => '01',
            'name' => 'PENERBITAN DAN PENYIARAN',
            'subcategories' => [
                [
                    'code' => '0101',
                    'name' => 'Penerbitan',
                    'mof_codes' => [
                        ['code' => '010101', 'name' => 'Bahan Bacaan Terbitan Luar Negara'],
                        ['code' => '010102', 'name' => 'Bahan Bacaan'],
                        ['code' => '010103', 'name' => 'Penerbitan Elektronik Atas Talian'],
                        ['code' => '010104', 'name' => 'Bahan Penerbitan Elektronik Dan Muzik/ Lagu (Siap Cetak)'],
                    ],
                ],
                [
                    'code' => '0102',
                    'name' => 'Kertas',
                    'mof_codes' => [
                        ['code' => '010201', 'name' => 'Kertas'],
                        ['code' => '010299', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '0103',
                    'name' => 'Peralatan Penerbitan/ Percetakan',
                    'mof_codes' => [
                        ['code' => '010301', 'name' => 'Peralatan Percetakan Serta Aksesori'],
                        ['code' => '010302', 'name' => 'Peralatan Sistem Bunyi, Pembesar Suara Dan Projektor'],
                        ['code' => '010303', 'name' => 'Peralatan/ Perkakasan Penyuntingan/ Persembahan'],
                        ['code' => '010304', 'name' => 'Medium Penyimpanan'],
                        ['code' => '010399', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '0104',
                    'name' => 'Papan Tanda Dan Aksesori',
                    'mof_codes' => [
                        ['code' => '010401', 'name' => 'Papan Tanda Dan Aksesori'],
                        ['code' => '010499', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '0105',
                    'name' => 'Fotografi Dan Filem',
                    'mof_codes' => [
                        ['code' => '010501', 'name' => 'Kamera Dan Aksesori'],
                        ['code' => '010502', 'name' => 'Peralatan Pemprosesan Fotografi, Mikrofilem'],
                        ['code' => '010503', 'name' => 'Filem Dan Mikrofilem'],
                        ['code' => '010504', 'name' => 'Filem Siap Untuk Tayangan (Lesen B FINAS - Pengedar)'],
                        ['code' => '010599', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '0106',
                    'name' => 'Peralatan Pendidikan Dan Latihan',
                    'mof_codes' => [
                        ['code' => '010601', 'name' => 'Kit Pendidikan'],
                        ['code' => '010602', 'name' => 'Bahan Pendidikan'],
                        ['code' => '010699', 'name' => 'Pembuat'],
                    ],
                ],
            ],
        ],
        [
            'code' => '02',
            'name' => 'PERABOT, PERALATAN PEJABAT, HIASAN DALAMAN DAN DOMESTIK',
            'subcategories' => [
                [
                    'code' => '0201',
                    'name' => 'Perabot, Kelengkapan Dan Aksesori',
                    'mof_codes' => [
                        ['code' => '020101', 'name' => 'Perabot, Perabot Makmal Dan Kelengkapan Berasaskan Kayu/ Rotan/ Fabrik/ Logam/ Plastik (Workstation)'],
                        ['code' => '020102', 'name' => 'Barangan Hiasan Dalaman Dan Aksesori'],
                        ['code' => '020103', 'name' => 'Permaidani/ Ambar'],
                        ['code' => '020199', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '0202',
                    'name' => 'Mesin-mesin Pejabat Dan Aksesori',
                    'mof_codes' => [
                        ['code' => '020201', 'name' => 'Mesin-mesin Pejabat Dan Aksesori'],
                        ['code' => '020299', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '0203',
                    'name' => 'Perkakas Elektrik Dan Elektronik',
                    'mof_codes' => [
                        ['code' => '020301', 'name' => 'Perkakas Elektrik Dan Aksesori'],
                        ['code' => '020302', 'name' => 'Perkakas Elektronik Dan Aksesori'],
                        ['code' => '020399', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '0204',
                    'name' => 'Peralatan Dan Perkakas Domestik',
                    'mof_codes' => [
                        ['code' => '020401', 'name' => 'Peralatan Dan Perkakas Domestik (Termasuk Barang-barang Yang Tidak Lekat Di Badan)'],
                        ['code' => '020402', 'name' => 'Perkakasan Dan Bahan Kebersihan Diri Dan Mandian, Kelengkapan Bilik Air Dan Aksesori'],
                        ['code' => '020403', 'name' => 'Bahan Pencuci Dan Pembersihan'],
                        ['code' => '020404', 'name' => 'Solekan Dan Andaman'],
                        ['code' => '020499', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '0205',
                    'name' => 'Bahan Pembungkusan/ Bekas',
                    'mof_codes' => [
                        ['code' => '020501', 'name' => 'Bahan Pembungkusan/ Bekas/ Kotak/ Palet'],
                        ['code' => '020599', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '0206',
                    'name' => 'Bekalan Pejabat Dan Alatulis',
                    'mof_codes' => [
                        ['code' => '020601', 'name' => 'Alatulis (Tidak Termasuk Borang Dan Semua Jenis Kertas)'],
                        ['code' => '020602', 'name' => 'Bahan Surih, Drafting Dan Alat Lukis'],
                        ['code' => '020603', 'name' => 'Organiser, Dairi, Kalendar, Buku Alamat, Resit, Memo'],
                        ['code' => '020604', 'name' => 'Tag/ Label/ Tanda Dan Stiker'],
                        ['code' => '020699', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '0207',
                    'name' => 'Tekstil',
                    'mof_codes' => [
                        ['code' => '020701', 'name' => 'Tekstil'],
                        ['code' => '020799', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '0208',
                    'name' => 'Pakaian Dan Kelengkapan',
                    'mof_codes' => [
                        ['code' => '020801', 'name' => 'Pakaian'],
                        ['code' => '020802', 'name' => 'Kelengkapan Pakaian'],
                        ['code' => '020803', 'name' => 'Bagasi Dan Beg Dari Kulit/ PVC/ Kanvas/ Kain/ Nylon/ Plastik/ Logam/ Dll'],
                        ['code' => '020804', 'name' => 'Pakaian Keselamatan, Kelengkapan Dan Aksesori'],
                        ['code' => '020899', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '0209',
                    'name' => 'Bahan Tarpaulin Dan Kanvas',
                    'mof_codes' => [
                        ['code' => '020901', 'name' => 'Bahan Tarpaulin Dan Kanvas'],
                        ['code' => '020999', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '0210',
                    'name' => 'Aksesori Dan Bekalan Jahitan',
                    'mof_codes' => [
                        ['code' => '021001', 'name' => 'Butang Dan Bekalan Jahitan (Kits)'],
                        ['code' => '021099', 'name' => 'Pembuat'],
                    ],
                ],
            ],
        ],
        [
            'code' => '03',
            'name' => 'SUKAN, REKREASI DAN ALAT MUZIK',
            'subcategories' => [
                [
                    'code' => '0301',
                    'name' => 'Pakaian Sukan Dan Aksesori',
                    'mof_codes' => [
                        ['code' => '030101', 'name' => 'Pakaian Sukan Dan Aksesori'],
                        ['code' => '030199', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '0302',
                    'name' => 'Cenderamata Dan Hadiah',
                    'mof_codes' => [
                        ['code' => '030201', 'name' => 'Cenderamata Dan Hadiah'],
                        ['code' => '030299', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '0303',
                    'name' => 'Alat Muzik',
                    'mof_codes' => [
                        ['code' => '030301', 'name' => 'Alat Muzik Dan Aksesori'],
                        ['code' => '030399', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '0304',
                    'name' => 'Peralatan Dan Aksesori Perkhemahan Dan Aktiviti Luar',
                    'mof_codes' => [
                        ['code' => '030401', 'name' => 'Peralatan Perkhemahan Dan Aktiviti Luar'],
                        ['code' => '030402', 'name' => 'Peralatan Memancing'],
                        ['code' => '030403', 'name' => 'Peralatan Memburu'],
                        ['code' => '030499', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '0305',
                    'name' => 'Peralatan Sukan Padang, Gelanggang, Rekreasi, Taman Permainan, Kecergasan Dan Sukan Air',
                    'mof_codes' => [
                        ['code' => '030501', 'name' => 'Peralatan Sukan'],
                        ['code' => '030599', 'name' => 'Pembuat'],
                    ],
                ],
            ],
        ],
        [
            'code' => '04',
            'name' => 'MAKANAN, MINUMAN DAN BAHAN MENTAH',
            'subcategories' => [
                [
                    'code' => '0401',
                    'name' => 'Makanan, Minuman Dan Bahan Mentah Kering/ Basah',
                    'mof_codes' => [
                        ['code' => '040101', 'name' => 'Makanan Dan Bahan Mentah Kering/ Basah'],
                        ['code' => '040102', 'name' => 'Makanan Dan Minuman (Tin, Botol Dan Bungkus)'],
                        ['code' => '040103', 'name' => 'Makanan Bermasak (Islam)'],
                        ['code' => '040104', 'name' => 'Makanan Bermasak (Bukan Islam)'],
                        ['code' => '040199', 'name' => 'Pembuat'],
                    ],
                ],
            ],
        ],
        [
            'code' => '05',
            'name' => 'PERALATAN HOSPITAL, PERUBATAN, UBAT-UBATAN DAN FARMASEUTIKAL',
            'subcategories' => [
                [
                    'code' => '0501',
                    'name' => 'Peralatan Hospital, Bahan Dan Kelengkapan Perubatan',
                    'mof_codes' => [
                        ['code' => '050101', 'name' => 'Peralatan Dan Kelengkapan Hospital'],
                        ['code' => '050102', 'name' => 'Peralatan Dan Kelengkapan Perubatan'],
                        ['code' => '050103', 'name' => 'Peralatan Untuk Orang Kurang Upaya Dan Pemulihan'],
                        ['code' => '050199', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '0502',
                    'name' => 'Ubat Dan Bahan Ubatan',
                    'mof_codes' => [
                        ['code' => '050201', 'name' => 'Dadah Berjadual [Perlu Lesen Di Bawah Peraturan-peraturan Kawalan Dadah Dan Kosmetik 1984 dari Kementerian Kesihatan Malaysia (KKM)]'],
                        ['code' => '050202', 'name' => 'Racun Berjadual (Lesen Akta Racun 1952 dari Pengarah Kesihatan Negeri)'],
                        ['code' => '050203', 'name' => 'Ubat Tidak Berjadual'],
                        ['code' => '050204', 'name' => 'Makanan/ Minuman Tambahan (Food Suppliment)'],
                        ['code' => '050299', 'name' => 'Pembuat [Perlu Lesen Pengilang (Borang 2) Dari KKM]'],
                    ],
                ],
                [
                    'code' => '0503',
                    'name' => 'Pekakas, Tekstil dan Pakaian Perubatan Pakai Buang/ Guna Semula',
                    'mof_codes' => [
                        ['code' => '050301', 'name' => 'Pekakas Perubatan Pakai Buang'],
                        ['code' => '050302', 'name' => 'Pakaian/ Tekstil Pakai Buang Kakitangan/ Pesakit'],
                        ['code' => '050303', 'name' => 'Pakaian/ Tekstil Guna Semula Kakitangan/ Pesakit'],
                        ['code' => '050399', 'name' => 'Pembuat'],
                    ],
                ],
            ],
        ],
        [
            'code' => '06',
            'name' => 'KIMIA, BAHAN KIMIA DAN PERALATAN MAKMAL',
            'subcategories' => [
                [
                    'code' => '0601',
                    'name' => 'Kimia',
                    'mof_codes' => [
                        ['code' => '060101', 'name' => 'Kimia Makmal'],
                        ['code' => '060102', 'name' => 'Kimia Industri'],
                        ['code' => '060103', 'name' => 'Kimia Memproses Air'],
                        ['code' => '060104', 'name' => 'Kimia Memproses Filem/ Fotografi'],
                        ['code' => '060199', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '0602',
                    'name' => 'Bahan Biokimia Dan Gas',
                    'mof_codes' => [
                        ['code' => '060201', 'name' => 'Bahan Peledak (Belerang, Pelarut Hidrokabon Dan Beroksigen/ Gunpowder)'],
                        ['code' => '060202', 'name' => 'Bunga Api Dan Mercun'],
                        ['code' => '060203', 'name' => 'Pencucuh/ Alat Penghasil Nyalaan'],
                        ['code' => '060204', 'name' => 'Gas (Industri Dan Domestik)'],
                        ['code' => '060205', 'name' => 'Pewarna/ Pencelup/ Lilin'],
                        ['code' => '060299', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '0603',
                    'name' => 'Bahan Bakar Dan Pelincir',
                    'mof_codes' => [
                        ['code' => '060301', 'name' => 'Bahan Bakar'],
                        ['code' => '060302', 'name' => 'Bahan Pelincir'],
                        ['code' => '060303', 'name' => 'Bahan Api Nuklear'],
                        ['code' => '060399', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '0604',
                    'name' => 'Cat, Anti Kakis Dan Bahan Tambah',
                    'mof_codes' => [
                        ['code' => '060401', 'name' => 'Cat'],
                        ['code' => '060402', 'name' => 'Anti Kakis/ Bahan Tambah'],
                        ['code' => '060499', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '0605',
                    'name' => 'Peralatan Makmal',
                    'mof_codes' => [
                        ['code' => '060501', 'name' => 'Peralatan Makmal Serta Aksesori'],
                        ['code' => '060502', 'name' => 'Peralatan Makmal Pengukuran, Pencerapan Dan Sukat'],
                        ['code' => '060599', 'name' => 'Pembuat'],
                    ],
                ],
            ],
        ],
        [
            'code' => '07',
            'name' => 'PERTANIAN, PERHUTANAN DAN TERNAKAN',
            'subcategories' => [
                [
                    'code' => '0701',
                    'name' => 'Baja Dan Racun',
                    'mof_codes' => [
                        ['code' => '070101', 'name' => 'Baja Dan Nutrien Tumbuhan (Organik/ Bukan Organik)'],
                        ['code' => '070102', 'name' => 'Racun Serangga/ Perosak, Rumpai/ Tumbuhan'],
                        ['code' => '070199', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '0702',
                    'name' => 'Tanaman, Ternakan, Baka Tanaman/ Ternakan Dan Sampel (Bahan Yang Telah Diawetkan)',
                    'mof_codes' => [
                        ['code' => '070201', 'name' => 'Tanaman/ Baka/ Benih Semaian'],
                        ['code' => '070202', 'name' => 'Haiwan Ternakan/ Bukan Ternakan Dan Akuatik'],
                        ['code' => '070203', 'name' => 'Sampel Dan Sampel Awetan Haiwan/ Akuatik/ Serangga/ Tumbuhan'],
                    ],
                ],
                [
                    'code' => '0703',
                    'name' => 'Ubat, Makanan Ternakan/ Tumbuhan, Peralatan Dan Aksesori',
                    'mof_codes' => [
                        ['code' => '070301', 'name' => 'Ubat Haiwan/ Akuatik'],
                        ['code' => '070302', 'name' => 'Makanan Haiwan/ Akuatik'],
                        ['code' => '070303', 'name' => 'Peralatan Dan Kelengkapan Pertanian/ Ternakan/ Akuatik'],
                        ['code' => '070304', 'name' => 'Hasil Sampingan Dan Sisa Perladangan'],
                        ['code' => '070305', 'name' => 'Habitat Dan Tempat Kurungan Haiwan'],
                        ['code' => '070306', 'name' => 'Peralatan Pengawalan Perosak Tanaman'],
                        ['code' => '070399', 'name' => 'Pembuat'],
                    ],
                ],
            ],
        ],
        [
            'code' => '08',
            'name' => 'KEJURUTERAAN AWAM, BINAAN DAN KELENGKAPAN KEMUDAHAN AWAM',
            'subcategories' => [
                [
                    'code' => '0801',
                    'name' => 'Kelengkapan/ Kemudahan Awam',
                    'mof_codes' => [
                        ['code' => '080101', 'name' => 'Kelengkapan/ Kemudahan Awam (Kecuali Kelengkapan Kemudahan Permainan/ Sukan)'],
                        ['code' => '080102', 'name' => 'Kontena'],
                        ['code' => '080199', 'name' => 'Pembuat'],
                    ],
                ],
            ],
        ],
        [
            'code' => '09',
            'name' => 'BAHAN BINAAN DAN PERALATAN KESELAMATAN JALAN RAYA',
            'subcategories' => [
                [
                    'code' => '0901',
                    'name' => 'Bahan Binaan',
                    'mof_codes' => [
                        ['code' => '090101', 'name' => 'Bahan Binaan'],
                        ['code' => '090102', 'name' => 'Paip Dan Kelengkapan'],
                        ['code' => '090199', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '0902',
                    'name' => 'Peralatan Keselamatan Jalan Raya',
                    'mof_codes' => [
                        ['code' => '090201', 'name' => 'Peralatan Keselamatan/ Perabot Jalan Raya'],
                        ['code' => '090299', 'name' => 'Pembuat Keselamatan/ Perabot Jalan Raya'],
                    ],
                ],
            ],
        ],
        [
            'code' => '10',
            'name' => 'PERALATAN SUKATAN DAN UKURAN',
            'subcategories' => [
                [
                    'code' => '1001',
                    'name' => 'Peralatan Sukatan Dan Ukuran',
                    'mof_codes' => [
                        ['code' => '100101', 'name' => 'Semua Peralatan Sukatan/ Ukuran'],
                        ['code' => '100199', 'name' => 'Pembuat'],
                    ],
                ],
            ],
        ],
        [
            'code' => '11',
            'name' => 'PENGANGKUTAN, KOMPONEN DAN AKSESORI',
            'subcategories' => [
                [
                    'code' => '1101',
                    'name' => 'Kenderaan Bermotor Dan Tidak Bermotor',
                    'mof_codes' => [
                        ['code' => '110101', 'name' => 'Basikal'],
                        ['code' => '110102', 'name' => 'Motosikal'],
                        ['code' => '110103', 'name' => 'Kereta'],
                        ['code' => '110104', 'name' => 'Lori'],
                        ['code' => '110105', 'name' => 'Bas'],
                        ['code' => '110106', 'name' => 'Kenderaan Kegunaan Khusus'],
                        ['code' => '110199', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '1102',
                    'name' => 'Jentera Berat',
                    'mof_codes' => [
                        ['code' => '110201', 'name' => 'Jentera Berat'],
                        ['code' => '110202', 'name' => 'Kren'],
                        ['code' => '110203', 'name' => 'Trailer Dan Aksesori'],
                        ['code' => '110299', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '1103',
                    'name' => 'Alat Ganti Dan Aksesori Kenderaan/ Jentera Berat',
                    'mof_codes' => [
                        ['code' => '110301', 'name' => 'Alat Ganti/ Aksesori Kenderaan'],
                        ['code' => '110302', 'name' => 'Alat Ganti/ Aksesori Jentera Berat'],
                        ['code' => '110303', 'name' => 'Enjin Kenderaan/ Jentera Berat'],
                        ['code' => '110304', 'name' => 'Peralatan Servis Dan Selenggara'],
                        ['code' => '110399', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '1104',
                    'name' => 'Kenderaan Ber Rel, Peralatan Dan Alat Ganti',
                    'mof_codes' => [
                        ['code' => '110401', 'name' => 'Kenderaan Ber Rel, Peralatan Dan Kereta Kabel'],
                        ['code' => '110402', 'name' => 'Lokomotif Dan Troli Elektrik'],
                        ['code' => '110403', 'name' => 'Sistem, Peralatan, Alat Ganti Keretapi Dan Aksesori'],
                        ['code' => '110499', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '1105',
                    'name' => 'Pesawat Udara, Kapal Terbang, Kapal Angkasa, Satelit, Radar',
                    'mof_codes' => [
                        ['code' => '110501', 'name' => 'Pesawat Udara'],
                        ['code' => '110502', 'name' => 'Helikopter'],
                        ['code' => '110503', 'name' => 'Alatganti Dan Kelengkapan Pesawat/ Helikopter'],
                        ['code' => '110504', 'name' => 'Kapal Angkasa Dan Alatganti'],
                        ['code' => '110505', 'name' => 'Satelit Dan Alatganti'],
                        ['code' => '110506', 'name' => 'Radar Dan Alatganti'],
                        ['code' => '110507', 'name' => 'Simulator'],
                        ['code' => '110599', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '1106',
                    'name' => 'Bot Dan Kapal',
                    'mof_codes' => [
                        ['code' => '110601', 'name' => 'Bot'],
                        ['code' => '110602', 'name' => 'Kapal Laut/ Kapal Selam'],
                        ['code' => '110603', 'name' => 'Alat Ganti Dan Kelengkapan Bot/ Kapal/ Kapal Selam'],
                        ['code' => '110604', 'name' => 'Simulator Bot/ Kapal/ Kapal Selam'],
                        ['code' => '110699', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '1107',
                    'name' => 'Peralatan Marin',
                    'mof_codes' => [
                        ['code' => '110701', 'name' => 'Peralatan Marin'],
                        ['code' => '110799', 'name' => 'Pembuat'],
                    ],
                ],
            ],
        ],
        [
            'code' => '12',
            'name' => 'PERTAHANAN DAN KESELAMATAN',
            'subcategories' => [
                [
                    'code' => '1201',
                    'name' => 'Senjata, Peluru, Bahan Letupan Dan Aksesori',
                    'mof_codes' => [
                        ['code' => '120101', 'name' => 'Senjata Api'],
                        ['code' => '120102', 'name' => 'Peluru Dan Bom'],
                        ['code' => '120103', 'name' => 'Aksesori Senjata Api'],
                        ['code' => '120104', 'name' => 'Bahan Letupan/ Complete Rounds'],
                        ['code' => '120199', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '1202',
                    'name' => 'Kelengkapan Sasaran',
                    'mof_codes' => [
                        ['code' => '120201', 'name' => 'Kelengkapan Sasaran'],
                        ['code' => '120299', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '1203',
                    'name' => 'Misil, Roket Dan Sub-Sistem',
                    'mof_codes' => [
                        ['code' => '120301', 'name' => 'Peluru Berpandu'],
                        ['code' => '120302', 'name' => 'Sub Sistem Roket'],
                        ['code' => '120303', 'name' => 'Pelancar Misil Dan Roket'],
                        ['code' => '120399', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '1204',
                    'name' => 'Peralatan Keselamatan Dan Penguatkuasaan',
                    'mof_codes' => [
                        ['code' => '120401', 'name' => 'Alat Keselamatan, Perlindungan Dan Kawalan'],
                        ['code' => '120402', 'name' => 'Alat Forensik Dan Aksesori'],
                        ['code' => '120499', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '1205',
                    'name' => 'Pengesanan, Pemantauan Dan Perlindungan',
                    'mof_codes' => [
                        ['code' => '120501', 'name' => 'Kunci, Perkakasan Perlindungan Dan Aksesori'],
                        ['code' => '120502', 'name' => 'Peralatan Pemantauan Dan Pengesanan'],
                        ['code' => '120503', 'name' => 'Lesen/ Pengenalan Dan Pas Keselamatan Bersalut (Laminated)'],
                        ['code' => '120599', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '1206',
                    'name' => 'Perlindungan Kebakaran',
                    'mof_codes' => [
                        ['code' => '120601', 'name' => 'Sistem Pencegah Kebakaran'],
                        ['code' => '120602', 'name' => 'Peralatan Kawalan Api'],
                        ['code' => '120699', 'name' => 'Pembuat'],
                    ],
                ],
            ],
        ],
        [
            'code' => '13',
            'name' => 'PERALATAN KEJURUTERAAN DAN MESIN PENGELUARAN',
            'subcategories' => [
                [
                    'code' => '1301',
                    'name' => 'Mesin, Kelengkapan Bengkel Dan Mesin Pengeluaran',
                    'mof_codes' => [
                        ['code' => '130101', 'name' => 'Mesin Dan Kelengkapan Bengkel'],
                        ['code' => '130102', 'name' => 'Mesin Dan Kelengkapan Khusus'],
                        ['code' => '130199', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '1302',
                    'name' => 'Janakuasa Elektrik Dan Peralatan Generator/ Alat Ganti Dan Bateri',
                    'mof_codes' => [
                        ['code' => '130201', 'name' => 'Janakuasa, Peralatan/ Alat Ganti/ Aksesori (Secondary)'],
                        ['code' => '130202', 'name' => 'Mesin Dan Kelengkapan Khusus'],
                        ['code' => '130299', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '1303',
                    'name' => 'Sistem Kumbahan',
                    'mof_codes' => [
                        ['code' => '130301', 'name' => 'Peralatan Sistem Kumbahan Dan Aksesori'],
                        ['code' => '130399', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '1304',
                    'name' => 'Peralatan Perindustrian Minyak',
                    'mof_codes' => [
                        ['code' => '130401', 'name' => 'Peralatan Perindustrian Huluan'],
                        ['code' => '130402', 'name' => 'Peralatan Perindustrian Hiliran'],
                        ['code' => '130499', 'name' => 'Pembuat'],
                    ],
                ],
            ],
        ],
        [
            'code' => '14',
            'name' => 'PERALATAN KEJURUTERAAN ELEKTRIK DAN ELEKTRONIK',
            'subcategories' => [
                [
                    'code' => '1401',
                    'name' => 'Mesin Dan Jentera Penjanaan Dan Pengagihan Tenaga Elektrik Serta Aksesori',
                    'mof_codes' => [
                        ['code' => '140101', 'name' => 'Motor Dan Alat Ubah/ Alat Ganti'],
                        ['code' => '140102', 'name' => 'Enjin, Komponen Enjin Dan Aksesori'],
                        ['code' => '140103', 'name' => 'Komponen Enjin Pembakaran Dalaman/ Gas Turbine'],
                        ['code' => '140199', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '1402',
                    'name' => 'Stesen Janakuasa Elektrik Dan Peralatan Generator/ Alat Ganti Dan Bateri',
                    'mof_codes' => [
                        ['code' => '140201', 'name' => 'Stesen Janakuasa, Peralatan/ Alat Ganti/ Aksesori (Primary)'],
                        ['code' => '140202', 'name' => 'Penjana Kuasa'],
                        ['code' => '140203', 'name' => 'Alat Penyimpan Tenaga Dan Aksesori'],
                        ['code' => '140299', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '1403',
                    'name' => 'Kabel, Wayar Elektrik Dan Aksesori',
                    'mof_codes' => [
                        ['code' => '140301', 'name' => 'Kabel Elektrik Dan Aksesori'],
                        ['code' => '140302', 'name' => 'Wayar Elektrik Dan Aksesori'],
                        ['code' => '140399', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '1404',
                    'name' => 'Peralatan Untuk Tenaga Atom Dan Nuklear',
                    'mof_codes' => [
                        ['code' => '140401', 'name' => 'Reaktor Dan Instrumen Nuklear'],
                        ['code' => '140499', 'name' => 'Pembuat'],
                    ],
                ],
                [
                    'code' => '1405',
                    'name' => 'Sistem, Komponen Elektrik, Elektronik, Lampu Dan Aksesori',
                    'mof_codes' => [
                        ['code' => '140501', 'name' => 'Sistem Elektronik'],
                        ['code' => '140502', 'name' => 'Komponen Dan Aksesori Elektrik/ Elektronik'],
                        ['code' => '140503', 'name' => 'Lampu, Komponen Lampu Dan Aksesori'],
                        ['code' => '140599', 'name' => 'Pembuat'],
                    ],
                ],
            ],
        ],
        [
            'code' => '22',
            'name' => 'PERKHIDMATAN',
            'subcategories' => [
                [
                    'code' => '2201',
                    'name' => 'Penyelenggaraan Dan Pembaikan Kenderaan',
                    'mof_codes' => [
                        ['code' => '220101', 'name' => 'Basikal (Tidak Perlu Lawatan Pengesahan)'],
                        ['code' => '220102', 'name' => 'Motosikal'],
                        ['code' => '220103', 'name' => 'Kenderaan Kegunaan Khusus (Seperti Kenderaan Rekreasi)'],
                        ['code' => '220104', 'name' => 'Kenderaan Bawah 3 Ton'],
                        ['code' => '220105', 'name' => 'Kenderaan Melebihi 3 Ton'],
                        ['code' => '220106', 'name' => 'Jentera Berat (Lori Pelarik Tanah, Roller Dan Forklift)'],
                        ['code' => '220107', 'name' => 'Kerja-Kerja Khusus (Baikpulih Enjin) Dan Sebagainya'],
                        ['code' => '220108', 'name' => 'Kerja-Kerja Mengetuk dan Mengecat'],
                        ['code' => '220109', 'name' => 'Alat Hawa Dingin Kenderaan'],
                        ['code' => '220110', 'name' => 'Membaik Pulih Tempat Duduk/ Kusyen Dan Bumbung'],
                        ['code' => '220111', 'name' => 'Kerja-Kerja Pembaikan Kenderaan Ber Rel Dan Kereta Kabel'],
                        ['code' => '220112', 'name' => 'Kerja-Kerja Penyelenggaraan Sistem Kenderaan'],
                        ['code' => '220113', 'name' => 'Membaik Pulih Tayar (Tidak Perlu Lawatan Pengesahan)'],
                        ['code' => '220114', 'name' => 'Membaik Pulih Bateri (Tidak Perlu Lawatan Pengesahan)'],
                        ['code' => '220115', 'name' => 'Kenderaan Pertahanan/ Keselamatan Negara – Motosikal'],
                        ['code' => '220116', 'name' => 'Kenderaan Pertahanan/ Keselamatan Negara – Kenderaan Kegunaan Khusus'],
                        ['code' => '220117', 'name' => 'Kenderaan Pertahanan/ Keselamatan Negara – Kenderaan Bawah 3 Ton'],
                        ['code' => '220118', 'name' => 'Kenderaan Pertahanan/ Keselamatan Negara – Kenderaan Melebihi 3 Ton'],
                        ['code' => '220119', 'name' => 'Kenderaan Pertahanan/ Keselamatan Negara – Jentera Berat'],
                        ['code' => '220120', 'name' => 'Kenderaan Pertahanan/ Keselamatan Negara – Kerja-Kerja Khusus (Baikpulih Enjin) Dan Sebagainya'],
                        ['code' => '220121', 'name' => 'Kenderaan Pertahanan/ Keselamatan Negara – Kerja-Kerja Mengetuk dan Mengecat'],
                        ['code' => '220122', 'name' => 'Kenderaan Pertahanan/ Keselamatan Negara – Alat Hawa Dingin Kenderaan'],
                        ['code' => '220123', 'name' => 'Kenderaan Pertahanan/ Keselamatan Negara – Membaik Pulih Tempat Duduk/ Kusyen dan Bumbung'],
                        ['code' => '220124', 'name' => 'Kenderaan Pertahanan/ Keselamatan Negara – Kerja-Kerja Penyelenggaraan Sistem Kenderaan'],
                    ],
                ],
                [
                    'code' => '2202',
                    'name' => 'Penyelenggaraan/ Pembaikan Mesin, Perabot Pejabat/ Kediaman',
                    'mof_codes' => [
                        ['code' => '220201', 'name' => 'Mesin-Mesin Pejabat/ Kediaman'],
                        ['code' => '220202', 'name' => 'Perabot Pejabat/ Kediaman'],
                        ['code' => '220203', 'name' => 'Alat Muzik, Kesenian Dan Aksesori'],
                    ],
                ],
                [
                    'code' => '2203',
                    'name' => 'Penyelenggaraan/ Pembaikan Alat Hawa Dingin',
                    'mof_codes' => [
                        ['code' => '220301', 'name' => 'Alat Hawa Dingin (Window/ Split/ Berpusat)'],
                    ],
                ],
                [
                    'code' => '2204',
                    'name' => 'Penyelenggaraan/ Pembaikan Alat Keselamatan',
                    'mof_codes' => [
                        ['code' => '220401', 'name' => 'Alat Kebombaan/ Alat Penyelamat/ Pemadam Api'],
                        ['code' => '220402', 'name' => 'Peralatan Kawalan Keselamatan'],
                        ['code' => '220403', 'name' => 'Mesin Pengimbas'],
                    ],
                ],
                [
                    'code' => '2205',
                    'name' => 'Penyelenggaraan/ Pembaikan Kejuruteraan Dan Komunikasi',
                    'mof_codes' => [
                        ['code' => '220501', 'name' => 'Alat Semboyan/ Perhubungan/ Penyiaran'],
                        ['code' => '220502', 'name' => 'Kontena/ Tangki'],
                        ['code' => '220503', 'name' => 'Perkakas/ Sistem Elektrik'],
                        ['code' => '220504', 'name' => 'Mesin dan Peralatan Woksyop'],
                        ['code' => '220505', 'name' => 'Mechanisation System'],
                        ['code' => '220506', 'name' => 'Membaiki Buff Fuel Tank'],
                        ['code' => '220507', 'name' => 'Pump/ Paip Air Dan Komponen'],
                        ['code' => '220508', 'name' => 'Baikpulih Barang-Barang Logam'],
                        ['code' => '220509', 'name' => 'Production Testing, Surface Well Testing and Wire Line Services'],
                        ['code' => '220510', 'name' => 'Faksimili'],
                    ],
                ],
                [
                    'code' => '2206',
                    'name' => 'Penyelenggaraan/ Pembaikan Peralatan/ Kelengkapan Perubatan dan Makmal',
                    'mof_codes' => [
                        ['code' => '220601', 'name' => 'Alat Kelengkapan Perubatan/ Makmal'],
                        ['code' => '220602', 'name' => 'Mesin Dan Peralatan Makmal'],
                    ],
                ],
                [
                    'code' => '2207',
                    'name' => 'Penyelenggaraan/ Pembaikan Bot/ Kapal, Helikopter, Simulator Dan Pesawat',
                    'mof_codes' => [
                        ['code' => '220701', 'name' => 'Bot/ Kapal/ Barge/ Kapal Selam/ Jet Ski/ Sampan (Limbungan/ Tanpa Limbungan)'],
                        ['code' => '220702', 'name' => 'Sand Blasting Dan Mengecat Untuk Kapal (Tidak Perlu Lawatan Pengesahan)'],
                        ['code' => '220703', 'name' => 'Penyelenggaraan Kapal Terbang'],
                        ['code' => '220704', 'name' => 'Penyelenggaraan Helikopter'],
                        ['code' => '220705', 'name' => 'Penyelenggaraan Simulator Kapal'],
                        ['code' => '220706', 'name' => 'Penyelenggaraan Simulator Kapal Terbang'],
                        ['code' => '220707', 'name' => 'Penyelenggaraan Simulator Helikopter'],
                        ['code' => '220708', 'name' => 'Pembaikan Kenderaan Yang Tidak Berenjin'],
                        ['code' => '220709', 'name' => 'Kerja Pembaikan Kapal Angkasa/ Satelit'],
                        ['code' => '220710', 'name' => 'Alat-Alat Marin (Tidak Termasuk Bot/ Kapal)'],
                        ['code' => '220711', 'name' => 'Kenderaan Pertahanan/ Keselamatan Negara – Bot/ Kapal/ Barge/ Kapal Selam /Jet Ski (Limbungan/ Tanpa Limbungan)'],
                        ['code' => '220712', 'name' => 'Kenderaan Pertahanan/ Keselamatan Negara – Sand Blasting Dan Mengecat Untuk Kapal'],
                        ['code' => '220713', 'name' => 'Kenderaan Pertahanan/ Keselamatan Negara – Penyelenggaraan Kapal Terbang'],
                        ['code' => '220714', 'name' => 'Kenderaan Pertahanan/ Keselamatan Negara – Penyelenggaraan Helikopter'],
                    ],
                ],
                [
                    'code' => '2208',
                    'name' => 'Pertahanan Dan Keselamatan',
                    'mof_codes' => [
                        ['code' => '220801', 'name' => 'Kawalan Keselamatan (Perlu lesen KDN)'],
                        ['code' => '220802', 'name' => 'Penyiasat Persendirian (Perlu lesen KDN)'],
                        ['code' => '220803', 'name' => 'Penyelenggaraan Dan Pembaikan Senjata'],
                        ['code' => '220804', 'name' => 'Penyelenggaraan Misil/ Roket Dan Sub Sistem, Pelancar'],
                    ],
                ],
                [
                    'code' => '2209',
                    'name' => 'Pengawalan Dan Pengawasan',
                    'mof_codes' => [
                        ['code' => '220901', 'name' => 'Kawalan Serangga Perosak, Anti Termite (Perlu Lesen Pengendali Kawalan Makhluk Perosak dari Jabatan Pertanian)'],
                        ['code' => '220902', 'name' => 'Menangkap/ Menembak Haiwan'],
                    ],
                ],
                [
                    'code' => '2210',
                    'name' => 'Khidmat Kebersihan Dan Rawatan',
                    'mof_codes' => [
                        ['code' => '221001', 'name' => 'Pembersihan Bangunan Dan Pejabat'],
                        ['code' => '221002', 'name' => 'Membersih Kawasan'],
                        ['code' => '221003', 'name' => 'Mengangkat Sampah'],
                        ['code' => '221004', 'name' => 'Membersih Kenderaan (Perlu Lesen PBT)'],
                        ['code' => '221005', 'name' => 'Mencuci Kolam Renang'],
                        ['code' => '221006', 'name' => 'Membersih Pantai/ Sungai/ Terusan/ Empangan/ Tasik'],
                        ['code' => '221007', 'name' => 'Pelupusan Dan Perawatan Sisa Berbahaya [Perlu Lesen daripada Lembaga Perlesenan Tenaga ATOM (AELB)]'],
                        ['code' => '221008', 'name' => 'Pelupusan Dan Perawatan Buangan Terjadual (Perlu Lesen daripada Jabatan Alam Sekitar)'],
                        ['code' => '221009', 'name' => 'Pelupusan dan Rawatan Sisa Radio Aktif dan Nuklear [Perlu Lesen daripada Lembaga Perlesenan Tenaga ATOM (AELB)]'],
                        ['code' => '221010', 'name' => 'Kolam Kumbahan/ Sisa Perawatan/ Talian Paip/ Sesalur'],
                        ['code' => '221011', 'name' => 'Pembersihan Tumpahan Minyak'],
                    ],
                ],
                [
                    'code' => '2211',
                    'name' => 'Guna Tenaga',
                    'mof_codes' => [
                        ['code' => '221101', 'name' => 'Kakitangan Iktisas (Profesional) - Tidak Termasuk Khidmat Perundingan'],
                        ['code' => '221102', 'name' => 'Kakitangan Separa Iktisas (Semi Profesional) - Tidak Termasuk Khidmat Perundingan'],
                        ['code' => '221103', 'name' => 'Khidmat Guaman'],
                        ['code' => '221104', 'name' => 'Tenaga Buruh'],
                        ['code' => '221105', 'name' => 'Pemungut Hutang/ Penghantar Notis'],
                        ['code' => '221106', 'name' => 'Stevedor'],
                        ['code' => '221107', 'name' => 'Telly Clerk'],
                        ['code' => '221108', 'name' => 'Mengikat Dan Melepas Tali Kapal (Mooring)'],
                        ['code' => '221109', 'name' => 'Menyelam (Diving Service)'],
                        ['code' => '221110', 'name' => 'Khidmat Latihan, Tenaga Pengajar dan Moderator/ Negotiator'],
                        ['code' => '221111', 'name' => 'Salvage Boat/ Kapal'],
                        ['code' => '221112', 'name' => 'Malim Kapal'],
                    ],
                ],
                [
                    'code' => '2212',
                    'name' => 'Khidmat Udara/ Laut/ Darat',
                    'mof_codes' => [
                        ['code' => '221201', 'name' => 'Topografi/ LIDAR'],
                        ['code' => '221202', 'name' => 'Pembajaan/ Pest Control'],
                        ['code' => '221203', 'name' => 'Cloud Seeding'],
                        ['code' => '221204', 'name' => 'Hidrografi'],
                        ['code' => '221205', 'name' => 'Oceanografi'],
                        ['code' => '221206', 'name' => 'Pemetaan/ Pemetaan Utiliti Bawah Tanah'],
                        ['code' => '221207', 'name' => 'Geologi'],
                    ],
                ],
                [
                    'code' => '2213',
                    'name' => 'Kesenian, Hiburan Dan Pelancongan',
                    'mof_codes' => [
                        ['code' => '221301', 'name' => 'Pengeluaran Filem (Perlu Lesen FINAS Borang A - Pengeluar)'],
                        ['code' => '221302', 'name' => 'Rakaman'],
                        ['code' => '221303', 'name' => 'Fotografi'],
                        ['code' => '221304', 'name' => 'Audio Visual'],
                        ['code' => '221305', 'name' => 'Penyediaan Pentas/ Pameran Pertunjukan, Taman Hiburan Dan Karnival/ Pestaria'],
                        ['code' => '221306', 'name' => 'Artis Dan Penghibur Profesional'],
                        ['code' => '221307', 'name' => 'Agen Pengembaraan (Dikhaskan Kepada Syarikat 100% Bumiputera)'],
                        ['code' => '221308', 'name' => 'Dokumentasi Dan Panduarah'],
                        ['code' => '221309', 'name' => 'Pemeliharaan Bahan Bahan Sejarah Dan Tempat Bersejarah'],
                        ['code' => '221310', 'name' => 'Penyimpanan Rekod (Surat Kelulusan Daripada Arkib Negara)'],
                        ['code' => '221311', 'name' => 'Membaikpulih Bahan Terbitan Dan Manuskrip (Surat Kelulusan Daripada Arkib Negara)'],
                    ],
                ],
                [
                    'code' => '2214',
                    'name' => 'Pengindahan',
                    'mof_codes' => [
                        ['code' => '221401', 'name' => 'Bangunan/ Hiasan Dalaman (Tidak Termasuk Pelanskapan Dan Seni Taman)'],
                        ['code' => '221402', 'name' => 'Hiasan Jalan/ Kawasan (Tidak Termasuk Pelanskapan Dan Seni Taman)'],
                    ],
                ],
                [
                    'code' => '2215',
                    'name' => 'Penyewaan Dan Pengurusan',
                    'mof_codes' => [
                        ['code' => '221501', 'name' => 'Perabot/ Kelengkapan'],
                        ['code' => '221502', 'name' => 'Mesin dan Peralatan Pejabat'],
                        ['code' => '221503', 'name' => 'Kenderaan/ Jentera/ Kenderaan Rekreasi'],
                        ['code' => '221504', 'name' => 'Kapal/ Bot/ Bot Tunda/ Feri/ Bot Malim/ Barge/ Jet Ski/ Kapal Selam'],
                        ['code' => '221505', 'name' => 'Kapal Terbang/ Helikopter/ Pesawat/ Belon Panas/ Simulator Serta Lain-Lain Kenderaan Udara'],
                        ['code' => '221506', 'name' => 'Bangunan/ Pejabat/ Stor/ Ruang Niaga/ Rumah Kediaman'],
                        ['code' => '221507', 'name' => 'Kemudahan Awam/ Sukan'],
                        ['code' => '221508', 'name' => 'Peralatan/ Kelengkapan Hospital Dan Makmal'],
                        ['code' => '221509', 'name' => 'Peralatan Keselamatan dan Senjata'],
                        ['code' => '221510', 'name' => 'Tempat Letak Kereta'],
                        ['code' => '221511', 'name' => 'P.A Sistem Dan Alat Muzik'],
                        ['code' => '221512', 'name' => 'Bantuan Kecemasan Dan Ambulans/ Kenderaan Jenazah'],
                        ['code' => '221513', 'name' => 'Pakaian/ Kelengkapan Dan Aksesori'],
                    ],
                ],
                [
                    'code' => '2216',
                    'name' => 'Percetakan',
                    'mof_codes' => [
                        ['code' => '221601', 'name' => 'Mencetak Buku, Majalah, Laporan Akhbar (Perlu Lesen KDN)'],
                        ['code' => '221602', 'name' => 'Mencetak Fail, Kad Perniagaan Dan Kad Ucapan (Perlu Lesen KDN)'],
                        ['code' => '221603', 'name' => 'Mencetak Label, Poster, Pelekat Dan Iron On (Perlu Lesen KDN)'],
                        ['code' => '221604', 'name' => 'Mencetak Label, Poster Dan Pelekat (Plastik) (Perlu Lesen KDN)'],
                        ['code' => '221605', 'name' => 'Mencetak Continuous Stationery Forms (Perlu Lesen KDN)'],
                        ['code' => '221606', 'name' => 'Mencetak Borang/Kertas Komputer (Perlu Lesen KDN)'],
                        ['code' => '221607', 'name' => 'Cetakan Keselamatan (Perlu Lesen KDN Dan Surat Kelulusan Pejabat Ketua Pengarah Keselamatan Kerajaan, Jabatan Perdana Menteri) (Dikhaskan Kepada Syarikat 100% Bumiputera)'],
                        ['code' => '221608', 'name' => 'Cetakan Hologram (Perlu Lesen KDN Dan Surat Kelulusan Pejabat Ketua Pengarah Keselamatan Kerajaan, Jabatan Perdana Menteri) (Dikhaskan Kepada Syarikat 100% Bumiputera)'],
                        ['code' => '221609', 'name' => 'Pisah Warna (Colour Separation)'],
                        ['code' => '221610', 'name' => 'Menjilid Kulit Keras'],
                        ['code' => '221611', 'name' => 'Varnishing'],
                        ['code' => '221612', 'name' => 'Laminating'],
                        ['code' => '221613', 'name' => 'Menjilid Kulit Lembut'],
                        ['code' => '221614', 'name' => 'Pengatur Huruf (Type Setting)'],
                        ['code' => '221615', 'name' => 'Rekabentuk Percetakan (Printing Design)'],
                    ],
                ],
                [
                    'code' => '2217',
                    'name' => 'Perkhidmatan Pengangkutan, Penyimpanan Dan Pos',
                    'mof_codes' => [
                        ['code' => '221701', 'name' => 'Pemilik Kapal (Perlu Sijil MCR)'],
                        ['code' => '221702', 'name' => 'Broker Perkapalan (Perjanjian Daripada Syarikat Perkapalan)'],
                        ['code' => '221703', 'name' => 'Agen Perkapalan (Perlu Lesen Kastam)'],
                        ['code' => '221704', 'name' => 'Pengangkutan Lori (Perlu Lesen APAD)'],
                        ['code' => '221705', 'name' => 'Agen Penghantaran (Perlu Lesen Kastam)'],
                        ['code' => '221706', 'name' => 'Pembungkusan Dan Penyimpanan (Perlu Gudang Berlesen Kastam Dan Lesen PBT)'],
                        ['code' => '221707', 'name' => 'Pembungkusan'],
                        ['code' => '221708', 'name' => 'Penghantaran Dokumen (Perlu Lesen Pos)'],
                        ['code' => '221709', 'name' => 'Multimodal Transport Operator (MTO)'],
                        ['code' => '221710', 'name' => 'Perkhidmatan Mel Pukal'],
                        ['code' => '221711', 'name' => 'Pengurusan Pelabuhan'],
                        ['code' => '221712', 'name' => 'Ship Chandling'],
                        ['code' => '221713', 'name' => 'Ship Trimming'],
                    ],
                ],
                [
                    'code' => '2218',
                    'name' => 'Perkhidmatan Kewangan Dan Insuran',
                    'mof_codes' => [
                        ['code' => '221801', 'name' => 'Syarikat Insuran (Perlu Lesen Bank Negara Malaysia)'],
                        ['code' => '221802', 'name' => 'Broker Insuran (Perlu Lesen Bank Negara Malaysia)'],
                        ['code' => '221803', 'name' => 'Penyediaan Akaun Dan Pengauditan'],
                        ['code' => '221804', 'name' => 'Pengurusan Kewangan Dan Korporat'],
                        ['code' => '221805', 'name' => 'Pemfaktoran (Dimansuhkan)'],
                        ['code' => '221806', 'name' => 'Syarikat Pelelong Awam (Perlu Lesen Pelelong PBT)'],
                    ],
                ],
                [
                    'code' => '2219',
                    'name' => 'Barang Lusuh',
                    'mof_codes' => [
                        ['code' => '221901', 'name' => 'Membeli Barang Lusuh Tanpa Permit'],
                        ['code' => '221902', 'name' => 'Membeli Barang Lusuh Perlu Permit (Perlu Permit PDRM)'],
                    ],
                ],
                [
                    'code' => '2220',
                    'name' => 'Editorial, Rekabentuk Grafik, Seni Halus Dan Harta Intelek',
                    'mof_codes' => [
                        ['code' => '222001', 'name' => 'Media Elektronik (Tidak Termasuk Kerja-kerja Percetakan)'],
                        ['code' => '222002', 'name' => 'Media Cetak (Tidak Termasuk Kerja-kerja Percetakan)'],
                        ['code' => '222003', 'name' => 'Bill Board'],
                        ['code' => '222004', 'name' => 'Penulisan – Semua Jenis Penulisan'],
                        ['code' => '222005', 'name' => 'Mereka-Cipta Dan Seni Halus'],
                        ['code' => '222006', 'name' => 'Penterjemahan'],
                        ['code' => '222007', 'name' => 'Pengkomersilan'],
                        ['code' => '222008', 'name' => 'Hak Harta Intelek (Patent)'],
                        ['code' => '222009', 'name' => 'Lain-lain Media Media Pengiklanan'],
                        ['code' => '222010', 'name' => 'Perkhidmatan Fotostat'],
                    ],
                ],
                [
                    'code' => '2221',
                    'name' => 'Perkhidmatan Perladangan/ Perikanan/ Haiwan Dan Hidupan Liar',
                    'mof_codes' => [
                        ['code' => '222101', 'name' => 'Perikanan Dan Akuakultur'],
                        ['code' => '222102', 'name' => 'Hortikultur'],
                        ['code' => '222103', 'name' => 'Ternakan'],
                        ['code' => '222104', 'name' => 'Pertanian/ Tanaman/ Ladang/ Taman/ Hutan Dan Ladang Hutan'],
                        ['code' => '222105', 'name' => 'Rawatan Hutan'],
                        ['code' => '222106', 'name' => 'Sumber Air'],
                        ['code' => '222107', 'name' => 'Tatahias Haiwan'],
                        ['code' => '222108', 'name' => 'Tukun Tiruan'],
                    ],
                ],
                [
                    'code' => '2222',
                    'name' => 'Perkhidmatan Hal Ehwal Sosial Dan Politik',
                    'mof_codes' => [
                        ['code' => '222201', 'name' => 'Hubungan Antarabangsa'],
                        ['code' => '222202', 'name' => 'Bantuan Kemanusiaan'],
                        ['code' => '222203', 'name' => 'Dasar Dan Peraturan'],
                    ],
                ],
                [
                    'code' => '2223',
                    'name' => 'Perkhidmatan Domestik',
                    'mof_codes' => [
                        ['code' => '222301', 'name' => 'Solekan'],
                        ['code' => '222302', 'name' => 'Dobi'],
                        ['code' => '222303', 'name' => 'Membekal Air'],
                        ['code' => '222304', 'name' => 'Pengurusan Jenazah Dan Kelengkapan'],
                        ['code' => '222305', 'name' => 'Mengangkut Mayat'],
                    ],
                ],
                [
                    'code' => '2224',
                    'name' => 'Perkhidmatan Menjahit Dan Baik Pulih',
                    'mof_codes' => [
                        ['code' => '222401', 'name' => 'Menjahit Pakaian Dan Kelengkapan'],
                        ['code' => '222402', 'name' => 'Menjahit Bukan Pakaian'],
                        ['code' => '222403', 'name' => 'Baik Pulih Kasut Dan Barangan Kulit'],
                        ['code' => '222404', 'name' => 'Barangan PVC/ Kanvas'],
                        ['code' => '222405', 'name' => 'Barangan Logam'],
                    ],
                ],
                [
                    'code' => '2225',
                    'name' => 'Hotel, Rumah Tumpangan Dan Pusat Latihan',
                    'mof_codes' => [
                        ['code' => '222501', 'name' => 'Hotel/ Resort (Perlu Sijil Pendaftaran Premis Penginapan bawah Akta Industri Pelancongan 1992 MOTAC dan Lesen PBT)'],
                        ['code' => '222502', 'name' => 'Motel/ Chalet/ Rumah Tumpangan (Perlu Lesen PBT)'],
                        ['code' => '222503', 'name' => 'Homestay (Perlu Surat Kementerian Pelancongan)'],
                        ['code' => '222504', 'name' => 'Pusat Latihan (Perlu Lesen PBT)'],
                    ],
                ],
                [
                    'code' => '2226',
                    'name' => 'Perkhidmatan Kejuruteraan Elektrik Dan Elektronik',
                    'mof_codes' => [
                        ['code' => '222601', 'name' => 'Akustik Dan Gelombang'],
                        ['code' => '222602', 'name' => 'Pencahayaan (Illumination)'],
                    ],
                ],
                [
                    'code' => '2227',
                    'name' => 'Perkhidmatan Lain-lain',
                    'mof_codes' => [
                        ['code' => '222701', 'name' => 'Pengurusan Telekomunikasi'],
                        ['code' => '222702', 'name' => 'Marker/ DNA'],
                        ['code' => '222703', 'name' => 'Bioteknologi'],
                        ['code' => '222704', 'name' => 'Pensijilan Dan Pengiktirafan'],
                        ['code' => '222705', 'name' => 'Ujian Makmal'],
                        ['code' => '222706', 'name' => 'Kodifikasi'],
                        ['code' => '222707', 'name' => 'Perkhidmatan Perubatan - Dialisis'],
                    ],
                ],
                [
                    'code' => '2228',
                    'name' => 'Perkhidmatan Teknologi Hijau',
                    'mof_codes' => [
                        ['code' => '222801', 'name' => 'Teknologi Hijau [Surat/ Sijil Daripada Suruhanjaya Tenaga (Energy Commission) atau Malaysia Green Technology Corporation]'],
                    ],
                ],
                [
                    'code' => '2229',
                    'name' => 'Seni Ukir',
                    'mof_codes' => [
                        ['code' => '222901', 'name' => 'Ukiran Berasaskan Kayu [Perlu Kemukakan Sijil Pendaftaran Dengan Perbadanan Kemajuan Kraftangan Malaysia (PKKM)]'],
                    ],
                ],
            ],
        ],
    ];

    public function run(): void
    {
        DB::transaction(function () {
            foreach ($this->mof_codes as $category) {
                $categoryModel = MofCategory::firstOrCreate(
                    ['code' => $category['code']],
                    ['name' => $category['name']]
                );

                foreach ($category['subcategories'] as $subcategory) {
                    // the subcategory['code'] also contain category code, so we need to remove the category code from the subcategory code: example 0101 -> 01, 0101 -> 01, 010101 -> 01, 010102 -> 02, 010103 -> 03, 010104 -> 04, 010105 -> 05, 010106 -> 06, 010107 -> 07, 010108 -> 08, 010109 -> 09, 010110 -> 10, 010111 -> 11, 010112 -> 12, 010113 -> 13, 010114 -> 14, 010115 -> 15, 010116 -> 16, 010117 -> 17, 010118 -> 18, 010119 -> 19, 010120 -> 20, etc.
                    $subcategory['code'] = substr($subcategory['code'], strlen($category['code']));

                    $subcategoryModel = MofSubcategory::firstOrCreate(
                        ['code' => $subcategory['code'], 'mof_category_id' => $categoryModel->id],
                        ['name' => $subcategory['name']]
                    );

                    foreach ($subcategory['mof_codes'] as $mofCode) {
                        MofCode::firstOrCreate(
                            ['code' => $mofCode['code'], 'mof_subcategory_id' => $subcategoryModel->id],
                            ['name' => $mofCode['name']]
                        );
                    }
                }
            }
        }, attempts: 1);
    }
}
