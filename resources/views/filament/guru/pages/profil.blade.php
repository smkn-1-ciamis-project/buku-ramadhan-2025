<x-filament-panels::page>
    <style>
        .gd-profil-container {
            max-width: 600px;
            margin: 0 auto;
        }
        .gd-profil-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
            overflow: hidden;
        }
        .gd-profil-header {
            background: linear-gradient(135deg, #166534 0%, #15803d 50%, #22c55e 100%);
            padding: 32px 24px;
            text-align: center;
            position: relative;
        }
        .gd-profil-avatar {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            border: 3px solid rgba(255,255,255,0.4);
        }
        .gd-profil-avatar span {
            font-size: 28px;
            font-weight: 700;
            color: #fff;
        }
        .gd-profil-header h3 {
            color: #fff;
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 4px;
        }
        .gd-profil-header p {
            color: rgba(255,255,255,0.8);
            font-size: 13px;
            margin: 0;
        }
        .gd-profil-body {
            padding: 24px;
        }
        .gd-profil-field {
            margin-bottom: 18px;
        }
        .gd-profil-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
        }
        .gd-profil-input {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            color: #1e293b;
            background: #f8fafc;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
            box-sizing: border-box;
        }
        .gd-profil-input:focus {
            border-color: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.12);
            background: #fff;
        }
        .gd-profil-hint {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 4px;
        }
        .gd-profil-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #166534 0%, #15803d 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.1s;
            margin-top: 6px;
        }
        .gd-profil-btn:hover {
            opacity: 0.9;
        }
        .gd-profil-btn:active {
            transform: scale(0.98);
        }
        .gd-profil-kelas {
            padding: 16px 24px;
            background: #f0fdf4;
            border-top: 1px solid #dcfce7;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .gd-profil-kelas-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #bbf7d0, #86efac);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .gd-profil-kelas-icon svg {
            width: 18px;
            height: 18px;
            color: #166534;
        }
        .gd-profil-kelas-text {
            flex: 1;
        }
        .gd-profil-kelas-label {
            font-size: 11px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .gd-profil-kelas-value {
            font-size: 14px;
            font-weight: 700;
            color: #166534;
        }
    </style>

    @php
        $guru = auth()->user();
        $kelasList = \App\Models\Kelas::where('wali_id', $guru->id)->pluck('nama')->toArray();
    @endphp

    <div class="gd-profil-container">
        <div class="gd-profil-card">
            <div class="gd-profil-header">
                <div class="gd-profil-avatar">
                    <span>{{ strtoupper(substr($guru->name ?? 'G', 0, 1)) }}</span>
                </div>
                <h3>{{ $guru->name }}</h3>
                <p>{{ $guru->email ?? 'Guru Wali Kelas' }}</p>
            </div>

            @if(count($kelasList))
                <div class="gd-profil-kelas">
                    <div class="gd-profil-kelas-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.57 50.57 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342"/></svg>
                    </div>
                    <div class="gd-profil-kelas-text">
                        <div class="gd-profil-kelas-label">Wali Kelas</div>
                        <div class="gd-profil-kelas-value">{{ implode(', ', $kelasList) }}</div>
                    </div>
                </div>
            @endif

            <form wire:submit="simpan" class="gd-profil-body">
                <div class="gd-profil-field">
                    <label class="gd-profil-label">Nama Lengkap</label>
                    <input type="text" wire:model="name" class="gd-profil-input" placeholder="Nama lengkap">
                    @error('name') <div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                </div>

                <div class="gd-profil-field">
                    <label class="gd-profil-label">Email</label>
                    <input type="email" wire:model="email" class="gd-profil-input" placeholder="email@contoh.com">
                    @error('email') <div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                </div>

                <div class="gd-profil-field">
                    <label class="gd-profil-label">No. HP / WhatsApp</label>
                    <input type="text" wire:model="no_hp" class="gd-profil-input" placeholder="08xxxxxxxxxx">
                    <div class="gd-profil-hint">Nomor ini akan ditampilkan ke siswa sebagai kontak wali kelas</div>
                    @error('no_hp') <div style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="gd-profil-btn">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</x-filament-panels::page>
