/** @type {import('tailwindcss').Config} */
export default {
  content: [
      './resources/views/**/*.blade.php',
      './resources/js/**/*.vue',
      "./src/**/*.{vue,js,ts,jsx,tsx}",
      'node_modules/flowbite-vue/**/*.{js,jsx,ts,tsx}'

  ],
  theme: {
    extend: {},
  },
  plugins: [
      require('flowbite/plugin')
  ],
}

