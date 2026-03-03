<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">المدرسون</h2>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap');
        * { font-family: 'Cairo', sans-serif; }

        :root {
            --accent: #e94560;
            --gold:   #f5a623;
            --muted:  #a8b2d8;
        }

        .page-bg { background: #0d1117; min-height: 100vh; padding-bottom: 80px; }

        /* Hero */
        .teachers-hero {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            padding: 56px 0 36px;
            position: relative; overflow: hidden;
        }
        .teachers-hero::before {
            content:''; position:absolute; top:-40%; right:-5%;
            width:360px; height:360px;
            background: radial-gradient(circle, rgba(233,69,96,.15) 0%, transparent 70%);
            border-radius:50%;
        }
        .hero-title { font-size:2.6rem; font-weight:900; color:#fff; }
        .hero-title span { color: var(--accent); }
        .hero-sub { color: var(--muted); font-size:1.05rem; margin-top:6px; }

        /* Filter bar */
        .filter-bar {
            background:#161b27;
            border-bottom:1px solid rgba(255,255,255,.06);
            padding:14px 0;
            position:sticky; top:0; z-index:50;
        }
        .filter-scroll { display:flex; gap:10px; overflow-x:auto; scrollbar-width:none; padding-bottom:2px; }
        .filter-scroll::-webkit-scrollbar { display:none; }

        .pill {
            flex-shrink:0;
            display:inline-flex; align-items:center; gap:6px;
            padding:7px 18px; border-radius:50px;
            font-size:.85rem; font-weight:700;
            border:1.5px solid rgba(255,255,255,.1);
            color: var(--muted); background:transparent;
            text-decoration:none; white-space:nowrap;
            transition: all .2s;
        }
        .pill:hover { border-color:var(--accent); color:#fff; background:rgba(233,69,96,.08); }
        .pill.active-all  { background:var(--gold); border-color:var(--gold); color:#000; }
        .pill.active-sub  { background:var(--accent); border-color:var(--accent); color:#fff; }

        /* Section */
        .section-header {
            display:flex; align-items:center; gap:14px; margin-bottom:26px;
        }
        .section-dot {
            width:11px; height:11px; border-radius:50%;
            background:var(--accent); box-shadow:0 0 10px var(--accent);
            flex-shrink:0;
        }
        .section-name { font-size:1.35rem; font-weight:800; color:#fff; }
        .section-count {
            font-size:.75rem; font-weight:700; color:var(--muted);
            background:rgba(255,255,255,.06); padding:2px 10px; border-radius:20px;
        }
        .section-line { flex:1; height:1px; background:linear-gradient(to left,transparent,rgba(255,255,255,.07)); }

        /* Card */
        .teacher-card {
            background:linear-gradient(145deg,#1a2035,#1e2a45);
            border:1px solid rgba(255,255,255,.07);
            border-radius:20px; overflow:hidden;
            text-decoration:none; display:block;
            position:relative;
            transition: transform .3s cubic-bezier(.4,0,.2,1), border-color .3s, box-shadow .3s;
        }
        .teacher-card::before {
            content:''; position:absolute; top:0; left:0; right:0; height:3px;
            background:linear-gradient(90deg,var(--accent),var(--gold));
            opacity:0; transition:opacity .3s;
        }
        .teacher-card:hover { transform:translateY(-6px); border-color:rgba(233,69,96,.3); box-shadow:0 20px 40px rgba(0,0,0,.4); }
        .teacher-card:hover::before { opacity:1; }

        .card-top { padding:22px 22px 0; display:flex; gap:14px; align-items:flex-start; }

        .avatar-wrap {
            width:68px; height:68px; border-radius:14px; flex-shrink:0;
            background:linear-gradient(135deg,var(--accent),#c23152);
            display:flex; align-items:center; justify-content:center;
            font-size:1.5rem; font-weight:900; color:#fff;
            border:2px solid rgba(255,255,255,.08);
            overflow:hidden;
        }
        .avatar-wrap img { width:100%; height:100%; object-fit:cover; }

        .teacher-name { font-size:1rem; font-weight:800; color:#fff; margin-bottom:5px; }
        .subject-tag {
            display:inline-block; font-size:.73rem; font-weight:700;
            padding:3px 10px; border-radius:20px;
            background:rgba(233,69,96,.12); color:var(--accent);
            border:1px solid rgba(233,69,96,.2);
        }

        .card-bio {
            padding:14px 22px;
            font-size:.83rem; color:var(--muted); line-height:1.75;
            display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
        }

        .card-footer {
            padding:14px 22px;
            border-top:1px solid rgba(255,255,255,.05);
            display:flex; align-items:center; justify-content:space-between;
        }
        .exp-badge { display:flex; align-items:center; gap:6px; font-size:.8rem; color:var(--muted); }
        .exp-badge strong { color:#fff; font-weight:800; }
        .view-btn { font-size:.8rem; font-weight:700; color:var(--accent); display:flex; align-items:center; gap:5px; transition:gap .2s; }
        .teacher-card:hover .view-btn { gap:9px; }

        /* Stars */
        .stars { color:var(--gold); font-size:.75rem; letter-spacing:1px; }

        /* Empty */
        .empty-state { text-align:center; padding:80px 20px; color:var(--muted); }

        @media(max-width:640px){ .hero-title{ font-size:1.75rem; } }
    </style>

    <div class="page-bg" dir="rtl">

        {{-- Hero --}}
        <div class="teachers-hero">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <h1 class="hero-title">تعلّم من <span>أفضل المدرسين</span></h1>
                <p class="hero-sub">اكتشف مدرسين متخصصين في مختلف المواد واختر من يناسبك</p>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="filter-bar">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="filter-scroll">
                    <a href="{{ route('teachers.index') }}"
                       class="pill {{ !$subjectFilter ? 'active-all' : '' }}">
                        ⭐ الكل
                    </a>
                    @foreach($subjects as $value => $label)
                        <a href="{{ route('teachers.index', ['subject' => $value]) }}"
                           class="pill {{ $subjectFilter === $value ? 'active-sub' : '' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12">

            @if($teachersBySubject->isEmpty())
                <div class="empty-state">
                    <div style="font-size:3rem;margin-bottom:16px">📭</div>
                    <p style="font-size:1.1rem;color:#fff;font-weight:800;margin-bottom:8px">
                        لا يوجد مدرسون حالياً
                    </p>
                    <p>تابعنا، سيتم إضافة مدرسين قريباً</p>
                </div>
            @else
                @foreach($teachersBySubject as $subjectLabel => $teachers)
                    <div class="mb-14" id="section-{{ Str::slug($subjectLabel) }}">

                        <div class="section-header">
                            <div class="section-dot"></div>
                            <span class="section-name">{{ $subjectLabel }}</span>
                            <span class="section-count">{{ $teachers->count() }} مدرس</span>
                            <div class="section-line"></div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                            @foreach($teachers as $teacher)
                                <a href="{{ route('teachers.show', $teacher) }}" class="teacher-card">

                                    <div class="card-top">
                                        <div class="avatar-wrap">
                                            @if($teacher->user->profile_image)
                                                <img src="{{ asset('storage/'.$teacher->user->profile_image) }}"
                                                     alt="{{ $teacher->user->name }}">
                                            @else
                                                {{ mb_substr($teacher->user->name, 0, 1) }}
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="teacher-name">{{ $teacher->user->name }}</div>
                                            <span class="subject-tag">{{ $subjectLabel }}</span>
                                        </div>
                                    </div>

                                    @if($teacher->bio)
                                        <p class="card-bio">{{ $teacher->bio }}</p>
                                    @endif

                                    <div class="card-footer">
                                        <div class="exp-badge">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                            <strong>{{ $teacher->years_of_experience }}</strong> سنوات خبرة
                                        </div>
                                        <span class="view-btn">
                                            عرض
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <line x1="19" y1="12" x2="5" y2="12"/>
                                                <polyline points="12 5 5 12 12 19"/>
                                            </svg>
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                    </div>
                @endforeach
            @endif
        </div>
    </div>
</x-app-layout>
