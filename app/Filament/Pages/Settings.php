<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Services\ServiceResource;
use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * @property-read Schema $form
 */
class Settings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Pengaturan';

    protected static ?string $title = 'Pengaturan Situs';

    protected static ?int $navigationSort = 10;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Setting::values());
    }

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            Section::make('Umum')->columns(2)->schema([
                TextInput::make('site_name')->label('Nama situs')->required()->maxLength(100),
                TextInput::make('tagline')->label('Tagline')->maxLength(150),
                FileUpload::make('logo')->label('Logo')->image()->maxSize(1024)->directory('settings'),
                Textarea::make('meta_description')->label('Meta description (SEO)')->maxLength(160)->rows(3),
            ]),
            Section::make('Hero beranda')->columns(2)->schema([
                TextInput::make('hero_title')->label('Judul')->required()->maxLength(150)->columnSpanFull(),
                Textarea::make('hero_subtitle')->label('Subjudul')->maxLength(300)->rows(3)->columnSpanFull(),
                Repeater::make('hero_images')->label('Gambar contoh aplikasi')->columnSpanFull()
                    ->helperText('Gambar tampil besar di sisi kanan hero. Lebih dari satu gambar berganti otomatis. Letakkan bagian penting di tengah agar terlihat saat dipotong.')
                    ->schema([
                        FileUpload::make('image')->label('Gambar')->image()->required()->maxSize(3072)->directory('hero')
                            ->imageResizeTargetWidth('1920')->helperText('Disarankan gambar persegi atau portrait. Gambar landscape akan dipotong mengikuti kartu.'),
                        TextInput::make('alt')->label('Deskripsi gambar')->required()->maxLength(150)
                            ->helperText('Untuk pembaca layar dan SEO, contoh: Dashboard aplikasi kasir di tablet.'),
                    ])
                    ->columns(2)->defaultItems(0)->maxItems(6)->reorderableWithButtons()->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['alt'] ?? null)
                    ->addActionLabel('Tambah gambar'),
            ]),
            Section::make('Keunggulan')->collapsible()
                ->description('3 pertama tampil di bawah judul hero; semuanya tampil di bagian keunggulan. Isi hanya klaim yang benar.')
                ->schema([
                    Repeater::make('strengths')->hiddenLabel()->schema([
                        Select::make('icon')->label('Ikon')->options(ServiceResource::ICONS)->required(),
                        TextInput::make('title')->label('Judul')->required()->maxLength(60),
                        Textarea::make('text')->label('Penjelasan')->maxLength(250)->rows(2)->columnSpanFull(),
                    ])->columns(2)->defaultItems(0)->maxItems(6)->reorderableWithButtons()->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)->addActionLabel('Tambah keunggulan'),
                ]),
            Section::make('Cara kerja')->collapsible()->schema([
                Repeater::make('steps')->hiddenLabel()->schema([
                    TextInput::make('title')->label('Langkah')->required()->maxLength(60),
                    Textarea::make('text')->label('Penjelasan')->maxLength(250)->rows(2),
                ])->defaultItems(0)->maxItems(5)->reorderableWithButtons()->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)->addActionLabel('Tambah langkah'),
            ]),
            Section::make('FAQ')->collapsible()->schema([
                Repeater::make('faqs')->hiddenLabel()->schema([
                    TextInput::make('question')->label('Pertanyaan')->required()->maxLength(150),
                    Textarea::make('answer')->label('Jawaban')->required()->maxLength(600)->rows(3),
                ])->defaultItems(0)->maxItems(12)->reorderableWithButtons()->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['question'] ?? null)->addActionLabel('Tambah pertanyaan'),
            ]),
            Section::make('Gambar pendukung beranda')->columns(3)->collapsible()
                ->description('Opsional. Section tetap tampil tanpa gambar.')
                ->schema([
                    FileUpload::make('intro_image')->label('Section "Tentang"')->image()->maxSize(3072)->directory('home')->imageResizeTargetWidth('1200'),
                    FileUpload::make('steps_image')->label('Section "Cara kerja"')->image()->maxSize(3072)->directory('home')->imageResizeTargetWidth('1200'),
                    FileUpload::make('cta_image')->label('Band ajakan kontak')->image()->maxSize(3072)->directory('home')->imageResizeTargetWidth('1200')
                        ->helperText('Paling bagus PNG transparan.'),
                ]),
            Section::make('Tentang perusahaan')->schema([
                Textarea::make('about')->label('Profil')->rows(4),
                Textarea::make('vision')->label('Visi')->rows(2),
                Textarea::make('mission')->label('Misi')->rows(4)->helperText('Satu poin per baris.'),
                Textarea::make('values')->label('Nilai perusahaan')->rows(4)->helperText('Satu nilai per baris.'),
            ]),
            Section::make('Kontak & sosial media')->columns(2)->schema([
                Textarea::make('address')->label('Alamat')->rows(2)->columnSpanFull(),
                TextInput::make('email')->label('Email')->email(),
                TextInput::make('phone')->label('Telepon')->maxLength(30),
                TextInput::make('whatsapp')->label('WhatsApp')->regex('/^\d{8,15}$/')
                    ->helperText('Angka saja dengan kode negara, contoh 6281234567890.'),
                TextInput::make('instagram')->label('Instagram URL')->url(),
                TextInput::make('linkedin')->label('LinkedIn URL')->url(),
                TextInput::make('map_embed_url')->label('URL embed Google Maps')->url()->startsWith('https://')
                    ->helperText('Google Maps > Bagikan > Sematkan peta, salin isi atribut src saja.'),
            ]),
        ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')->label('Simpan')->submit('save')->keyBindings(['mod+s']),
                    ]),
                ]),
        ]);
    }

    public function save(): void
    {
        Setting::saveMany($this->form->getState());

        Notification::make()->success()->title('Pengaturan disimpan')->send();
    }
}
