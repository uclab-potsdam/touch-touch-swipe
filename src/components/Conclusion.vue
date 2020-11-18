<template>
  <section class="conclusion">
    <div class="wrapper">
      <h3>Conclusion</h3>
      <div class="columns">
        <Paragraph
          v-for="(paragraph, index) in conclusion[language]"
          :key="index"
          :text="paragraph"
        />
      </div>
      <hr>
      <a href="https://fh-potsdam.de/"><img src="../assets/fhp.svg"></a>
      <p v-if="language === 'de'">Die Datenvisualisierung ›touch, touch, swipe‹ entstand während der Projektwoche ›Data Talks / Data Walks‹ bei Francesca Morini und Prof. Marian Dörk.</p>
      <p v-else>The data visualization ‘touch, touch, swipe’ is a result of the three-week project course ‘Data Talks / Data Walks’ by Francesca Morini and Prof. Marian Dörk.</p>
      <p><strong>Team</strong></p>
      <ul>
        <li>Tim Hönig</li>
        <li>Anna Meide</li>
        <li>Vi Quan Ta Doan</li>
        <li>Jona Pomerance</li>
      </ul>
    </div>
  </section>
</template>

<script>
import Paragraph from './Paragraph.vue';

export default {
  name: 'Conclusion',

  components: {
    Paragraph
  },

  props: {
    conclusion: {
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
.conclusion {
  position: relative;
  min-height: 100vh;
  padding: 115px 0 124px;
  box-sizing: border-box;
  color: #eee;
  background: #090909;
}

.conclusion h3 {
  margin: 0 0 10px;
  font-size: 34px;
  line-height: 1;
  text-transform: uppercase;
  letter-spacing: 0.02em;
  color: #090909;
  -webkit-text-stroke: 1px #eee;
  text-stroke: 1px #eee;
}

.conclusion p {
  width: 440px;
  margin: 22px 0 0;
  -webkit-hyphens: auto;
  hyphens: auto;
}

.conclusion .columns {
  columns: 2;
  column-gap: 80px;
}

.conclusion .columns p {
  display: inline-block;
}

.conclusion hr {
  margin: 38px -2px 36px;
  border: 1px solid transparent;
  border-top-color: rgba(255,255,255,0.8);
}

.conclusion ul {
  margin: 4px 0 0;
  padding: 0;
  list-style: none;
}

.conclusion ul li {
  display: inline-block;
  margin-right: 12px;
}

.conclusion img {
  float: right;
  width: 260px;
  margin-top: 4px;
}
</style>