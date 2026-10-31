# Finora Workspace Rules & Guidelines

<!-- antislop:start -->
## Antislop Enforcement (Always Active)

Selalu gunakan dan terapkan **skill antislop** yang telah terinstall di lingkungan ini (`~/.gemini/config/plugins/antislop/`). Antislop berfungsi sebagai filter kualitas untuk mengeliminasi generic AI slop (desain generik, gradien klise, layout monoton, teks marketing buatan AI, komentar kode yang mubazir, dan masalah aksesibilitas).

### Pemetaan Skill Antislop per Kategori Tugas:
Sebelum mengeksekusi tugas UI, copy, layout, aksesibilitas, atau penulisan kode, baca dan terapkan skill yang sesuai:
1. **Core Filter (Wajib Selalu Aktif)**: `antislop`
   - File: `~/.gemini/config/plugins/antislop/skills/antislop/SKILL.md`
   - Fungsi: Filter mendasar agar hasil tidak terkesan buatan AI template generik.
2. **UI & Visual**: `antislop-ui`
   - File: `~/.gemini/config/plugins/antislop/skills/antislop-ui/SKILL.md`
   - Fungsi: Pemilihan warna, layout kartu, dekorasi, hierarki visual, dan state interaktif.
3. **Copywriting & Bahasa**: `antislop-copywriting`
   - File: `~/.gemini/config/plugins/antislop/skills/antislop-copywriting/SKILL.md`
   - Fungsi: Penulisan headline, microcopy, label tombol/CTA, empty state, pesan error/sukses tanpa nada klise AI.
4. **Aksesibilitas & Human**: `antislop-human`
   - File: `~/.gemini/config/plugins/antislop/skills/antislop-human/SKILL.md`
   - Fungsi: Rasio kontras warna (WCAG AA > 4.5:1), aksesibilitas keyboard, fokus state, dan ukuran tap target (min 44x44px).
5. **Mobile & Tata Letak Responsif**: `antislop-layoutmobile`
   - File: `~/.gemini/config/plugins/antislop/skills/antislop-layoutmobile/SKILL.md`
   - Fungsi: Breakpoint ponsel/tablet/desktop, ergonomi satu tangan, bottom sheet, bottom navigation bar, dan penanganan overflow.
6. **Kebersihan Komentar Kode**: `antislop-code`
   - File: `~/.gemini/config/plugins/antislop/skills/antislop-code/SKILL.md`
   - Fungsi: Hapus komentar naratif yang jelas terlihat dari kode, pertahankan komentar logika domain yang krusial tanpa merusak kode.

### Mode Penggunaan (Usage Mode)
- **Mode Default**: `during`
- Sebelum melakukan perubahan/pembuatan UI atau teks, umumkan status mode satu kali:
  `antislop active: during (session override).` atau `antislop active: during (global preference).`
- Terapkan filter antislop secara proaktif selama proses pembuatan kode dan antarmuka.

### Arah Desain Finora (Design Direction)
- Wajib menyelaraskan dengan dokumentasi [DESIGN.md](/DESIGN.md).
- **Skala Dial Antislop**:
  - `ENERGY: 2` (Refined, fokus, tenang, percaya diri).
  - `RHYTHM: 2` (Variasi kartu ringkasan, baris dompet, dan daftar transaksi dengan whitespace yang proporsional).
  - `MOTION: 1` (Transisi tajam 150-200ms; hindari animasi melayang atau berlebihan).
- **Stack UI**: Tailwind CSS v4, DaisyUI, Alpine.js.
- **Tema**: `emerald-clean` (Light) & `night-slate` (Dark).
<!-- antislop:end -->
