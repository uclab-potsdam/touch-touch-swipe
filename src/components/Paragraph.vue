<template>
  <p v-html="formatParagraphText(text)" />
</template>

<script>
export default {
  name: 'Paragraph',

  props: {
    text: {
      type: String,
      default: ''
    }
  },
  
  methods: {
    formatParagraphText(text) {
      return text.replace(/\*\*(.*)\*\*/gim, '<strong>$1</strong>');
    }
  }
}
</script>