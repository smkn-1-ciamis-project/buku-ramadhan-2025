<?php

return [

  /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

  'accepted' => ':attribute harus diterima.',
  'accepted_if' => ':attribute harus diterima bila :other adalah :value.',
  'active_url' => ':attribute bukan URL yang valid.',
  'after' => ':attribute harus tanggal setelah :date.',
  'after_or_equal' => ':attribute harus tanggal setelah atau sama dengan :date.',
  'alpha' => ':attribute hanya boleh berisi huruf.',
  'alpha_dash' => ':attribute hanya boleh berisi huruf, angka, strip, dan garis bawah.',
  'alpha_num' => ':attribute hanya boleh berisi huruf dan angka.',
  'array' => ':attribute harus berupa array.',
  'ascii' => ':attribute hanya boleh berisi karakter alfanumerik dan simbol byte tunggal.',
  'before' => ':attribute harus tanggal sebelum :date.',
  'before_or_equal' => ':attribute harus tanggal sebelum atau sama dengan :date.',
  'between' => [
    'array' => ':attribute harus di antara :min dan :max item.',
    'file' => ':attribute harus di antara :min dan :max kilobyte.',
    'numeric' => ':attribute harus di antara :min dan :max.',
    'string' => ':attribute harus di antara :min dan :max karakter.',
  ],
  'boolean' => ':attribute harus bernilai true atau false.',
  'can' => ':attribute berisi nilai yang tidak diizinkan.',
  'confirmed' => 'Konfirmasi :attribute tidak cocok.',
  'contains' => ':attribute tidak memiliki nilai yang diperlukan.',
  'current_password' => 'Kata sandi salah.',
  'date' => ':attribute bukan tanggal yang valid.',
  'date_equals' => ':attribute harus tanggal yang sama dengan :date.',
  'date_format' => ':attribute tidak cocok dengan format :format.',
  'decimal' => ':attribute harus memiliki :decimal angka desimal.',
  'declined' => ':attribute harus ditolak.',
  'declined_if' => ':attribute harus ditolak bila :other adalah :value.',
  'different' => ':attribute dan :other harus berbeda.',
  'digits' => ':attribute harus :digits digit.',
  'digits_between' => ':attribute harus di antara :min dan :max digit.',
  'dimensions' => ':attribute tidak memiliki dimensi gambar yang valid.',
  'distinct' => ':attribute memiliki nilai duplikat.',
  'doesnt_end_with' => ':attribute tidak boleh diakhiri dengan salah satu dari: :values.',
  'doesnt_start_with' => ':attribute tidak boleh diawali dengan salah satu dari: :values.',
  'email' => ':attribute harus alamat surel yang valid.',
  'ends_with' => ':attribute harus diakhiri dengan salah satu dari: :values.',
  'enum' => ':attribute yang dipilih tidak valid.',
  'exists' => ':attribute yang dipilih tidak valid.',
  'extensions' => ':attribute harus memiliki salah satu ekstensi: :values.',
  'file' => ':attribute harus berupa file.',
  'filled' => ':attribute harus memiliki nilai.',
  'gt' => [
    'array' => ':attribute harus lebih dari :value item.',
    'file' => ':attribute harus lebih dari :value kilobyte.',
    'numeric' => ':attribute harus lebih dari :value.',
    'string' => ':attribute harus lebih dari :value karakter.',
  ],
  'gte' => [
    'array' => ':attribute harus memiliki :value item atau lebih.',
    'file' => ':attribute harus lebih dari atau sama dengan :value kilobyte.',
    'numeric' => ':attribute harus lebih dari atau sama dengan :value.',
    'string' => ':attribute harus lebih dari atau sama dengan :value karakter.',
  ],
  'hex_color' => ':attribute harus warna heksadesimal yang valid.',
  'image' => ':attribute harus berupa gambar.',
  'in' => ':attribute yang dipilih tidak valid.',
  'in_array' => ':attribute harus ada di :other.',
  'integer' => ':attribute harus berupa bilangan bulat.',
  'ip' => ':attribute harus alamat IP yang valid.',
  'ipv4' => ':attribute harus alamat IPv4 yang valid.',
  'ipv6' => ':attribute harus alamat IPv6 yang valid.',
  'json' => ':attribute harus berupa JSON string yang valid.',
  'list' => ':attribute harus berupa daftar.',
  'lowercase' => ':attribute harus huruf kecil.',
  'lt' => [
    'array' => ':attribute harus kurang dari :value item.',
    'file' => ':attribute harus kurang dari :value kilobyte.',
    'numeric' => ':attribute harus kurang dari :value.',
    'string' => ':attribute harus kurang dari :value karakter.',
  ],
  'lte' => [
    'array' => ':attribute tidak boleh lebih dari :value item.',
    'file' => ':attribute harus kurang dari atau sama dengan :value kilobyte.',
    'numeric' => ':attribute harus kurang dari atau sama dengan :value.',
    'string' => ':attribute harus kurang dari atau sama dengan :value karakter.',
  ],
  'mac_address' => ':attribute harus alamat MAC yang valid.',
  'max' => [
    'array' => ':attribute tidak boleh lebih dari :max item.',
    'file' => ':attribute tidak boleh lebih dari :max kilobyte.',
    'numeric' => ':attribute tidak boleh lebih dari :max.',
    'string' => ':attribute tidak boleh lebih dari :max karakter.',
  ],
  'max_digits' => ':attribute tidak boleh lebih dari :max digit.',
  'mimes' => ':attribute harus berupa file bertipe: :values.',
  'mimetypes' => ':attribute harus berupa file bertipe: :values.',
  'min' => [
    'array' => ':attribute harus memiliki setidaknya :min item.',
    'file' => ':attribute harus setidaknya :min kilobyte.',
    'numeric' => ':attribute harus setidaknya :min.',
    'string' => ':attribute harus setidaknya :min karakter.',
  ],
  'min_digits' => ':attribute harus memiliki setidaknya :min digit.',
  'missing' => ':attribute tidak boleh ada.',
  'missing_if' => ':attribute tidak boleh ada bila :other adalah :value.',
  'missing_unless' => ':attribute tidak boleh ada kecuali :other adalah :value.',
  'missing_with' => ':attribute tidak boleh ada bila :values ada.',
  'missing_with_all' => ':attribute tidak boleh ada bila :values ada.',
  'multiple_of' => ':attribute harus kelipatan dari :value.',
  'not_in' => ':attribute yang dipilih tidak valid.',
  'not_regex' => 'Format :attribute tidak valid.',
  'numeric' => ':attribute harus berupa angka.',
  'password' => [
    'letters' => ':attribute harus mengandung setidaknya satu huruf.',
    'mixed' => ':attribute harus mengandung setidaknya satu huruf besar dan satu huruf kecil.',
    'numbers' => ':attribute harus mengandung setidaknya satu angka.',
    'symbols' => ':attribute harus mengandung setidaknya satu simbol.',
    'uncompromised' => ':attribute yang diberikan telah muncul dalam kebocoran data. Silakan pilih :attribute yang berbeda.',
  ],
  'present' => ':attribute harus ada.',
  'present_if' => ':attribute harus ada bila :other adalah :value.',
  'present_unless' => ':attribute harus ada kecuali :other adalah :value.',
  'present_with' => ':attribute harus ada bila :values ada.',
  'present_with_all' => ':attribute harus ada bila :values ada.',
  'prohibited' => ':attribute tidak boleh ada.',
  'prohibited_if' => ':attribute tidak boleh ada bila :other adalah :value.',
  'prohibited_unless' => ':attribute tidak boleh ada kecuali :other ada di :values.',
  'prohibits' => ':attribute melarang :other untuk ada.',
  'regex' => 'Format :attribute tidak valid.',
  'required' => ':attribute wajib diisi.',
  'required_array_keys' => ':attribute harus berisi entri untuk: :values.',
  'required_if' => ':attribute wajib diisi bila :other adalah :value.',
  'required_if_accepted' => ':attribute wajib diisi bila :other diterima.',
  'required_if_declined' => ':attribute wajib diisi bila :other ditolak.',
  'required_unless' => ':attribute wajib diisi kecuali :other ada di :values.',
  'required_with' => ':attribute wajib diisi bila :values ada.',
  'required_with_all' => ':attribute wajib diisi bila :values ada.',
  'required_without' => ':attribute wajib diisi bila :values tidak ada.',
  'required_without_all' => ':attribute wajib diisi bila tidak ada :values yang ada.',
  'same' => ':attribute dan :other harus sama.',
  'size' => [
    'array' => ':attribute harus berisi :size item.',
    'file' => ':attribute harus :size kilobyte.',
    'numeric' => ':attribute harus :size.',
    'string' => ':attribute harus :size karakter.',
  ],
  'starts_with' => ':attribute harus diawali dengan salah satu dari: :values.',
  'string' => ':attribute harus berupa string.',
  'timezone' => ':attribute harus zona waktu yang valid.',
  'unique' => ':attribute sudah digunakan.',
  'uploaded' => ':attribute gagal diunggah.',
  'uppercase' => ':attribute harus huruf besar.',
  'url' => ':attribute harus URL yang valid.',
  'ulid' => ':attribute harus ULID yang valid.',
  'uuid' => ':attribute harus UUID yang valid.',

  /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

  'custom' => [
    'attribute-name' => [
      'rule-name' => 'custom-message',
    ],
  ],

  /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

  'attributes' => [
    'nisn' => 'NISN',
    'password' => 'Kata Sandi',
    'email' => 'Surel',
    'name' => 'Nama',
    'username' => 'Nama Pengguna',
    'first_name' => 'Nama Depan',
    'last_name' => 'Nama Belakang',
    'age' => 'Umur',
    'address' => 'Alamat',
    'phone' => 'Telepon',
    'mobile' => 'Ponsel',
    'date' => 'Tanggal',
    'day' => 'Hari',
    'month' => 'Bulan',
    'year' => 'Tahun',
    'hour' => 'Jam',
    'minute' => 'Menit',
    'second' => 'Detik',
    'title' => 'Judul',
    'content' => 'Konten',
    'description' => 'Deskripsi',
    'excerpt' => 'Kutipan',
    'date_of_birth' => 'Tanggal Lahir',
    'city' => 'Kota',
    'country' => 'Negara',
    'province' => 'Provinsi',
    'postal_code' => 'Kode Pos',
    'subject' => 'Subjek',
    'message' => 'Pesan',
  ],

];
