<div>
    <table>
    <thead>
        <tr>
            <th>NO</th>
            <th>NAMA</th>
            <th>PANGKAT/GOL</th>
            <th>TMT PANGKAT</th>
            <th>CORPS</th>
            <th>NRP/NIP</th>
            <th>JABATAN</th>
            <th>TMT JABATAN</th>
            <th>SATUAN</th>
            <th>TMT TNI/PNS</th>
            <th>SUKU</th>
            <th>AGAMA</th>
            <th>TGL LAHIR</th>
            <th>TEMPAT LAHIR</th>
            <th>MKG</th>
            <th>JENIS KELAMIN</th>
            <th>DIKUM/PENDIDIKAN</th>
            <th>THN LULUS DIKUM</th>
            <th>DIK PERTAMA TNI</th>
            <th>TAHUN LULUS DIK PERTAMA TNI</th>
            <th>DIKMIL TI</th>
            <th>TAHUN LULUS DIKMIL TI</th>
            <th>KETERANGAN</th>
        </tr>
    </thead>
    <tbody>
        @foreach($personels as $i => $p)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $p->nama }}</td>
            <td>{{ $p->pangkat_golongan }}</td>
            <td>{{ $p->tmt_pangkat }}</td>
            <td>{{ $p->corps }}</td>
            <td>{{ $p->nrp_nip }}</td>
            <td>{{ $p->jabatan }}</td>
            <td>{{ $p->tmt_jabatan }}</td>
            <td>{{ $p->satuan_bagian }}</td>
            <td>{{ $p->tmt_tni_pa }}</td>
            <td>{{ $p->suku }}</td>
            <td>{{ $p->agama }}</td>
            <td>{{ $p->tgl_lahir ? $p->tgl_lahir->format('d/m/Y') : '' }}</td>
            <td>{{ $p->tempat_lahir }}</td>
            <td>{{ $p->mkg }}</td>
            <td>{{ $p->jenis_kelamin }}</td>
            <td>{{ $p->dikum_ti }}</td>
            <td>{{ $p->thn_lulus_dikum }}</td>
            <td>{{ $p->dikmit_tni }}</td>
            <td>{{ $p->thn_lulus_dikmit }}</td>
            <td>{{ $p->pendidikan_lanjutan }}</td>
            <td>{{ $p->tahun_lulus_lanjutan }}</td>
            <td>{{ $p->ket }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
