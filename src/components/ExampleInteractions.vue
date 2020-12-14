<template>
  <div class="interactions">
    <div class="previous-3">
      <span v-if="language === 'de'">Vorheriger Post</span>
      <span v-else>Previous Post</span>
    </div>
    <div class="previous-2">
      <span v-if="language === 'de'">Nächstes Karussell-Bild</span>
      <span v-else>Next Carousel Image</span>
    </div>
    <div class="previous-1">
      <span v-if="language === 'de'">Nächster Post</span>
      <span v-else>Next Post</span>
    </div>
    <div class="previous-0">
      <span v-if="language === 'de'">Touch auf Post</span>
      <span v-else>Tap on Post</span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ExampleInteractions',

  props: {
    language: {
      type: String,
      default: () => 'en'
    }
  }
}
</script>

<style></style>