<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Something went wrong') - InvoiceHero</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background:
                radial-gradient(circle at top left, rgba(99, 102, 241, 0.16), transparent 28%),
                radial-gradient(circle at bottom right, rgba(16, 185, 129, 0.14), transparent 24%),
                linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);
        }

        .grid-pattern {
            background-image:
                linear-gradient(rgba(99, 102, 241, 0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99, 102, 241, 0.06) 1px, transparent 1px);
            background-size: 28px 28px;
        }

        .float-slow {
            animation: floatSlow 6s ease-in-out infinite;
        }

        .pulse-soft {
            animation: pulseSoft 2.8s ease-in-out infinite;
        }

        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        @keyframes pulseSoft {
            0%, 100% { transform: scale(1); opacity: 0.95; }
            50% { transform: scale(1.04); opacity: 1; }
        }
    </style>
</head>
<body class="min-h-screen text-gray-900">
    <main class="grid-pattern min-h-screen px-4 py-10 sm:px-6 lg:px-8">
        <div class="mx-auto flex min-h-[calc(100vh-5rem)] max-w-6xl items-center">
            <div class="grid w-full gap-8 lg:grid-cols-[1.2fr_0.8fr]">
                <section class="rounded-[2rem] border border-white/70 bg-white/85 p-8 shadow-2xl shadow-indigo-100/70 backdrop-blur md:p-12">
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-indigo-100 bg-indigo-50 px-4 py-2 text-xs font-bold uppercase tracking-[0.2em] text-indigo-700">
                        <i class="fas fa-bolt"></i>
                        InvoiceHero
                    </div>

                    @yield('content')
                </section>

                <aside class="relative overflow-hidden rounded-[2rem] border border-slate-200/80 bg-slate-950 p-8 text-white shadow-2xl shadow-slate-300/40 md:p-10">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(99,102,241,0.35),_transparent_38%),radial-gradient(circle_at_bottom,_rgba(16,185,129,0.22),_transparent_28%)]"></div>
                    <div class="relative z-10 flex h-full flex-col justify-between">
                        <div>
                            <div class="mb-6 inline-flex h-16 w-16 items-center justify-center rounded-3xl bg-white/10 text-2xl text-indigo-200 pulse-soft">
                                <i class="@yield('panel_icon', 'fas fa-circle-exclamation')"></i>
                            </div>

                            <div class="float-slow">
                                <div class="rounded-[1.75rem] border border-white/10 bg-white/5 p-6">
                                    <div class="mb-4 flex items-center gap-3">
                                        <div class="h-3 w-3 rounded-full bg-emerald-400"></div>
                                        <div class="h-3 w-3 rounded-full bg-amber-400"></div>
                                        <div class="h-3 w-3 rounded-full bg-rose-400"></div>
                                    </div>

                                    @yield('illustration')
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 text-sm text-slate-300">
                            <p class="font-semibold text-white">@yield('panel_title', 'We are handling it')</p>
                            <p class="mt-2 leading-6">@yield('panel_copy', 'Use the navigation below to continue browsing safely.')</p>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </main>
</body>
</html>
