<!-- resources/views/partials/tailwind-cdn.blade.php -->

<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Inter', 'system-ui', 'sans-serif'],
                    mono: ['JetBrains Mono', 'monospace'],
                },
                colors: {
                    brand: {
                        50: '#f5f3ff', 100: '#ede9fe', 200: '#ddd6fe', 300: '#c4b5fd',
                        400: '#a78bfa', 500: '#8b5cf6', 600: '#7c3aed', 700: '#6d28d9',
                        800: '#5b21b6', 900: '#4c1d95', 950: '#2e1065',
                    },
                    surface: {
                        900: '#0f172a', 800: '#1e293b', 700: '#334155', 600: '#475569',
                        500: '#64748b', 400: '#94a3b8', 300: '#cbd5e1', 200: '#e2e8f0',
                        100: '#f1f5f9', 50: '#f8fafc',
                    },
                },
            },
        },
    }
</script>