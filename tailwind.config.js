module.exports = {
  purge: [
    './resources/views/**/*.blade.php',
    './resources/css/**/*.css',
  ],
  theme: {
    extend: {
        colors: {
            'primary': '#1a58f4',
            'secondary': '#1c2c4b',
            'green-balance': '#08b458'
        },
        fontFamily: {
            'sans': [
                'SF-Pro-Rounded',
                'system-ui', '-apple-system', 'BlinkMacSystemFont',
                '"Segoe UI"', 'Roboto', '"Helvetica Neue"',
                'Arial', '"Noto Sans"', 'sans-serif', '"Apple Color Emoji"',
                '"Segoe UI Emoji"', '"Segoe UI Symbol"', '"Noto Color Emoji"',
            ],
        }
    },


  },
  variants: {},
  plugins: [
    require('@tailwindcss/custom-forms')
  ]
}
