/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/views/**/*.php",
    "./resources/js/**/*.js",
    "./public/**/*.php",
    "./admin/**/*.php"
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#fdf2f4', 100: '#fbe4e9', 200: '#f7cdd7',
          300: '#efa6b7', 400: '#e4738e', 500: '#c94465',
          600: '#a42c4d', 700: '#841f3b', 800: '#6f1c32',
          900: '#5f1b2d', 950: '#350a16', DEFAULT: '#841f3b',
        },
        secondary: {
          50: '#f8fafc', 100: '#f1f5f9', 200: '#e2e8f0',
          300: '#cbd5e1', 400: '#94a3b8', 500: '#64748b',
          600: '#475569', 700: '#334155', 800: '#1e293b',
          900: '#0f172a', 950: '#020617', DEFAULT: '#64748b',
        },
        dark: {
          50: '#f8fafc', 100: '#f1f5f9', 200: '#e2e8f0',
          300: '#cbd5e1', 400: '#94a3b8', 500: '#64748b',
          600: '#475569', 700: '#334155', 800: '#1e293b',
          900: '#0f172a', 950: '#020617', DEFAULT: '#0f172a',
        },
        light: {
          50: '#ffffff', 100: '#f8fafc', 200: '#f1f5f9',
          300: '#e2e8f0', 400: '#cbd5e1', 500: '#94a3b8',
          600: '#64748b', 700: '#475569', 800: '#334155',
          900: '#1e293b', DEFAULT: '#f8fafc',
        },
        danger: {
          50: '#fef2f2', 100: '#fee2e2', 200: '#fecaca',
          300: '#fca5a5', 400: '#f87171', 500: '#ef4444',
          600: '#dc2626', 700: '#b91c1c', 800: '#991b1b',
          900: '#7f1d1d', 950: '#450a0a', DEFAULT: '#dc2626',
        },
        info: {
          50: '#ecfeff', 100: '#cffafe', 200: '#a5f3fc',
          300: '#67e8f9', 400: '#22d3ee', 500: '#06b6d4',
          600: '#0891b2', 700: '#0e7490', 800: '#155e75',
          900: '#164e63', 950: '#083344', DEFAULT: '#0891b2',
        },
        success: {
          50: '#f0fdf4', 100: '#dcfce7', 200: '#bbf7d0',
          500: '#22c55e', 600: '#16a34a', 700: '#15803d',
          DEFAULT: '#16a34a',
        },
        warning: {
          50: '#fffbeb', 100: '#fef3c7', 200: '#fde68a',
          500: '#f59e0b', 600: '#d97706', 700: '#b45309',
          DEFAULT: '#d97706',
        },
      },
      borderRadius: {
        button: '0.375rem',
        card: '0.5rem',
      },
      boxShadow: {
        card: '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
      },
      maxWidth: {
        container: '1280px',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        slideUp: {
          '0%': { opacity: '0', transform: 'translateY(20px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        float: {
          '0%, 100%': { transform: 'translateY(0)' },
          '50%': { transform: 'translateY(-10px)' },
        }
      },
      animation: {
        fadeIn: 'fadeIn 1s ease-out forwards',
        slideUp: 'slideUp 0.8s ease-out forwards',
        float: 'float 4s ease-in-out infinite',
      }
    },
  },
  plugins: [],
}
