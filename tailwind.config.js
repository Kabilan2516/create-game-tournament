export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {},
  },
  plugins: [
      require('@tailwindcss/aspect-ratio'),
  ],
}
