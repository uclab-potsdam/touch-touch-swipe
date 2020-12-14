<template>
  <section class="introduction" ref="introduction">
    <div class="wrapper">
      <img src="../assets/logo.svg" width="232" height="232">
      <Paragraph
        v-for="(paragraph, index) in introduction[language]"
        :key="index"
        :text="paragraph"
      />
    </div>
  </section>
</template>

<script>
import Paragraph from './Paragraph.vue';

export default {
  name: 'Introduction',

  components: {
    Paragraph
  },

  props: {
    introduction: {
      type: Object,
      default: () => {}
    },
    language: {
      type: String,
      default: () => 'en'
    }
  }
}
</script>

<style>
.introduction {
  /*min-height: 100vh;*/
  padding: 108px 0 124px;
  background: #fff;
  box-sizing: border-box;
}

/*@media (min-height: 1100px) {
  .introduction {
    min-height: unset;
  }
}*/

.introduction img {
  margin: 0 0 27px 102px;
}

.introduction p {
  width: 440px;
  margin: 20px 0 0;
  -webkit-hyphens: auto;
  hyphens: auto;
}
</style>