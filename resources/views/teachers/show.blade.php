<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $teacher->user->name }}</h2>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap');
        * { font-family:'Cairo',sans-serif; }
        :root { --accent:#e94560; --gold:#f5a623; --muted:#a8b2d8; }

        .page-bg { background:#0d1117; min-height:100vh; padding-bottom:80px; }

        .back-link {
            display:inline-flex; align-items:center; gap:8px;
            color:var(--muted); font-size:.88rem; font-weight:600;
            text-decoration:none; padding:10px 0; transition:color .2s;
        }
        .back-link:hover { color:#fff; }

        /* Profile card */
        .profile-card {
            background:linear-gradient(145deg,#1a2035,#1e2a45);
            border:1px solid rgba(255,255,255,.07);
            border-radius:24px; padding:32px; margin-bottom:22px;
            position:relative; overflow:hidden;
        }
        .profile-card::before {
            content:''; position:absolute; top:0; right:0;
            width:280px; height:280px;
            background:radial-gradient(circle,rgba(233,69,96,.07) 0%,transparent 70%);
        }

        .profile-avatar {
            width:100px; height:100px; border-radius:18px;
            background:linear-gradient(135deg,var(--accent),#c23152);
            display:flex; align-items:center; justify-content:center;
            font-size:2.2rem; font-weight:900; color:#fff;
            flex-shrink:0; overflow:hidden;
            border:2px solid rgba(255,255,255,.1);
        }
        .profile-avatar img { width:100%; height:100%; object-fit:cover; }

        .profile-name { font-size:1.75rem; font-weight:900; color:#fff; margin-bottom:8px; }

        .subject-badge {
            display:inline-flex; align-items:center; gap:6px;
            font-size:.88rem; font-weight:700; padding:5px 14px; border-radius:50px;
            background:rgba(233,69,96,.12); color:var(--accent);
            border:1px solid rgba(233,69,96,.22); margin-bottom:16px;
        }

        .stats-row { display:flex; gap:28px; flex-wrap:wrap; margin-top:18px; }
        .stat-item { text-align:center; }
        .stat-num { font-size:1.5rem; font-weight:900; color:#fff; line-height:1; }
        .stat-lbl { font-size:.75rem; color:var(--muted); margin-top:3px; }
        .stat-sep { width:1px; background:rgba(255,255,255,.08); align-self:stretch; }

        /* Info card */
        .info-card {
            background:linear-gradient(145deg,#1a2035,#1e2a45);
            border:1px solid rgba(255,255,255,.07);
            border-radius:20px; padding:26px; margin-bottom:20px;
        }
        .card-title {
            font-size:.95rem; font-weight:800; color:#fff; margin-bottom:16px;
            display:flex; align-items:center; gap:10px;
        }
        .card-title::after { content:''; flex:1; height:1px; background:rgba(255,255,255,.06); }

        .bio-text { font-size:.9rem; color:var(--muted); line-height:2; }

        .info-row {
            display:flex; align-items:center; gap:12px;
            padding:13px 0; border-bottom:1px solid rgba(255,255,255,.05);
        }
        .info-row:last-child { border-bottom:none; }
        .info-icon {
            width:36px; height:36px; border-radius:10px;
            background:rgba(233,69,96,.1);
            display:flex; align-items:center; justify-content:center; flex-shrink:0;
        }
        .info-lbl { font-size:.75rem; color:var(--muted); margin-bottom:1px; }
        .info-val { font-size:.9rem; font-weight:700; color:#fff; }

        /* Reviews */
        .review-item {
            padding:16px 0; border-bottom:1px solid rgba(255,255,255,.05);
        }
        .review-item:last-child { border-bottom:none; }
        .review-header { display:flex; align-items:center; gap:10px; margin-bottom:8px; }
        .reviewer-avatar {
            width:36px; height:36px; border-radius:10px;
            background:linear-gradient(135deg,#0f3460,#16213e);
            display:flex; align-items:center; justify-content:center;
            font-size:.9rem; font-weight:800; color:#fff; flex-shrink:0;
        }
        .reviewer-name { font-size:.88rem; font-weight:700; color:#fff; }
        .review-date { font-size:.72rem; color:var(--muted); }
        .stars { color:var(--gold); font-size:.8rem; letter-spacing:1px; }
        .review-comment { font-size:.85rem; color:var(--muted); line-height:1.75; }

        /* CTA */
        .cta-card {
            background:linear-gradient(135deg,#e94560,#c23152);
            border-radius:20px; padding:26px; margin-bottom:20px;
            text-align:center; position:relative; overflow:hidden;
        }
        .cta-card::before {
            content:''; position:absolute; top:-40px; right:-40px;
            width:140px; height:140px;
            background:rgba(255,255,255,.07); border-radius:50%;
        }
        .cta-title { font-size:1.1rem; font-weight:800; color:#fff; margin-bottom:6px; }
        .cta-desc { font-size:.82rem; color:rgba(255,255,255,.8); margin-bottom:18px; }
        .btn-sub {
            display:block; width:100%; padding:13px;
            background:#fff; color:var(--accent);
            font-size:.95rem; font-weight:800; border-radius:12px;
            text-decoration:none; transition:transform .2s, box-shadow .2s;
        }
        .btn-sub:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(0,0,0,.3); }

        /* Related */
        .related-card {
            background:linear-gradient(145deg,#1a2035,#1e2a45);
            border:1px solid rgba(255,255,255,.07);
            border-radius:14px; padding:14px;
            display:flex; align-items:center; gap:12px;
            text-decoration:none; transition:all .2s;
        }
        .related-card:hover { border-color:rgba(233,69,96,.3); transform:translateX(-4px); }
        .related-av {
            width:44px; height:44px; border-radius:10px; flex-shrink:0;
            background:linear-gradient(135deg,var(--accent),#c23152);
            display:flex; align-items:center; justify-content:center;
            font-size:1rem; font-weight:800; color:#fff; overflow:hidden;
        }
        .related-av img { width:100%; height:100%; object-fit:cover; }
        .related-name { font-size:.88rem; font-weight:700; color:#fff; margin-bottom:2px; }
        .related-exp { font-size:.75rem; color:var(--muted); }

        /* Rating bar */
        .rating-bar-row { display:flex; align-items:center; gap:10px; margin-bottom:8px; }
        .rating-bar-label { font-size:.78rem; color:var(--muted); width:24px; text-align:left; flex-shrink:0; }
        .rating-bar-track { flex:1; height:6px; background:rgba(255,255,255,.07); border-radius:3px; overflow:hidden; }
        .rating-bar-fill { height:100%; background:var(--gold); border-radius:3px; }
        .rating-bar-count { font-size:.75rem; color:var(--muted); width:20px; text-align:right; flex-shrink:0; }
    </style>

    <div class="page-bg" dir="rtl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <a href="{{ route('teachers.index') }}" class="back-link mb-6 inline-flex">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                </svg>
                العودة للمدرسين
            </a>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Main Column --}}
                <div class="lg:col-span-2 space-y-5">

                    {{-- Profile --}}
                    <div class="profile-card">
                        <div class="flex gap-5 items-start relative z-10 flex-wrap">
                            <div class="profile-avatar">
                                @if($teacher->user->profile_image)
                                    <img src="{{ asset('storage/'.$teacher->user->profile_image) }}"
                                         alt="{{ $teacher->user->name }}">
                                @else
                                    {{ mb_substr($teacher->user->name, 0, 1) }}
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h1 class="profile-name">{{ $teacher->user->name }}</h1>
                                <div class="subject-badge">
                                    📚
                                    {{ $teacher->subject instanceof \App\Enums\Subject
                                        ? $teacher->subject->label()
                                        : $teacher->subject }}
                                </div>

                                @php
                                    $avg = round($teacher->reviews_avg_rating ?? 0, 1);
                                    $count = $teacher->reviews_count ?? 0;
                                @endphp

                                @if($count > 0)
                                    <div class="flex items-center gap-8 mt-1">
                                        <span class="stars">
                                            @for($i=1;$i<=5;$i++)
                                                {{ $i <= round($avg) ? '★' : '☆' }}
                                            @endfor
                                        </span>
                                        <span style="font-size:.82rem;color:var(--muted)">
                                            {{ $avg }} ({{ $count }} تقييم)
                                        </span>
                                    </div>
                                @endif

                                <div class="stats-row">
                                    <div class="stat-item">
                                        <div class="stat-num">{{ $teacher->years_of_experience }}</div>
                                        <div class="stat-lbl">سنوات خبرة</div>
                                    </div>
                                    @if($teacher->hourly_rate)
                                        <div class="stat-sep"></div>
                                        <div class="stat-item">
                                            <div class="stat-num">{{ number_format($teacher->hourly_rate) }}</div>
                                            <div class="stat-lbl">ج.م / ساعة</div>
                                        </div>
                                    @endif
                                    @if($count > 0)
                                        <div class="stat-sep"></div>
                                        <div class="stat-item">
                                            <div class="stat-num">{{ $avg }}</div>
                                            <div class="stat-lbl">تقييم الطلاب</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Bio --}}
                    @if($teacher->bio)
                        <div class="info-card">
                            <div class="card-title">نبذة عن المدرس</div>
                            <p class="bio-text">{{ $teacher->bio }}</p>
                        </div>
                    @endif

                    {{-- Details --}}
                    <div class="info-card">
                        <div class="card-title">معلومات إضافية</div>

                        @if($teacher->user->phone)
                            <div class="info-row">
                                <div class="info-icon">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#e94560" stroke-width="2">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.81a16 16 0 0 0 6 6l.86-.86a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.73 16.92z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="info-lbl">رقم التواصل</div>
                                    <div class="info-val">{{ $teacher->user->phone }}</div>
                                </div>
                            </div>
                        @endif

                        <div class="info-row">
                            <div class="info-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#e94560" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                </svg>
                            </div>
                            <div>
                                <div class="info-lbl">سنوات الخبرة</div>
                                <div class="info-val">{{ $teacher->years_of_experience }} سنة</div>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#e94560" stroke-width="2">
                                    <rect x="2" y="3" width="20" height="14" rx="2"/>
                                    <polyline points="8 21 12 17 16 21"/>
                                </svg>
                            </div>
                            <div>
                                <div class="info-lbl">التخصص</div>
                                <div class="info-val">
                                    {{ $teacher->subject instanceof \App\Enums\Subject
                                        ? $teacher->subject->label()
                                        : $teacher->subject }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Reviews --}}
                    @if($reviews->isNotEmpty())
                        <div class="info-card">
                            <div class="card-title">تقييمات الطلاب</div>

                            @foreach($reviews as $review)
                                <div class="review-item">
                                    <div class="review-header">
                                        <div class="reviewer-avatar">
                                            {{ mb_substr($review->student->user->name ?? '؟', 0, 1) }}
                                        </div>
                                        <div class="flex-1">
                                            <div class="reviewer-name">
                                                {{ $review->student->user->name ?? 'طالب' }}
                                            </div>
                                            <div class="review-date">
                                                {{ $review->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                        <div class="stars">
                                            @for($i=1;$i<=5;$i++)
                                                {{ $i <= $review->rating ? '★' : '☆' }}
                                            @endfor
                                        </div>
                                    </div>
                                    @if($review->comment)
                                        <p class="review-comment">{{ $review->comment }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>

                {{-- Sidebar --}}
                <div class="space-y-5">

                    {{-- CTA --}}
                    <div class="cta-card">
                        <div class="cta-title relative z-10">
                            اشترك مع {{ Str::words($teacher->user->name, 1, '') }}
                        </div>
                        <p class="cta-desc relative z-10">ابدأ رحلتك التعليمية واستفد من خبرة المدرس</p>
                        <a href="#" class="btn-sub relative z-10">🎓 اشترك الآن</a>
                    </div>

                    {{-- Related --}}
                    @if($relatedTeachers->isNotEmpty())
                        <div class="info-card">
                            <div class="card-title">مدرسون في نفس المادة</div>
                            <div class="space-y-3">
                                @foreach($relatedTeachers as $rel)
                                    <a href="{{ route('teachers.show', $rel) }}" class="related-card">
                                        <div class="related-av">
                                            @if($rel->user->profile_image)
                                                <img src="{{ asset('storage/'.$rel->user->profile_image) }}"
                                                     alt="{{ $rel->user->name }}">
                                            @else
                                                {{ mb_substr($rel->user->name, 0, 1) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="related-name">{{ $rel->user->name }}</div>
                                            <div class="related-exp">{{ $rel->years_of_experience }} سنوات خبرة</div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
