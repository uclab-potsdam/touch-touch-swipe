<template>
  <section class="conclusion">
    <div class="wrapper">
      <h3 v-if="language ==='de'">Konklusion</h3>
      <h3 v-else>Conclusion</h3>
      <div class="columns">
        <Paragraph
          v-for="(paragraph, index) in conclusion[language]"
          :key="index"
          :text="paragraph"
        />
      </div>
      <hr>
      <a href="https://fh-potsdam.de/"><img src="../assets/fhp.svg" width="260"></a>
      <a href="https://uclab.fh-potsdam.de/"><img src="../assets/uclab.svg" width="148"></a>
      <p v-if="language === 'de'">Das Datenvisualisierungsprojekt ›touch, touch, swipe‹ hatte seinen Ursprung in der Projektwoche ›Data Talks / Data Walks‹ bei <a href="https://morini.design/">Francesca Morini</a> und Prof. Marian Dörk.</p>
      <p v-else>The data visualization project ‘touch, touch, swipe’ originated from the three-week project course ‘Data Talks / Data Walks’ by <a href="https://morini.design/">Francesca Morini</a> and Prof. Marian Dörk.</p>
      <p><strong>Team</strong></p>
      <ul>
        <li><a href="https://honigmitoe.de/">Tim Hönig</a></li>
        <li><a href="http://annameide.com/">Anna Meide</a></li>
        <li>Tạ Đoàn Vi Quân</li>
        <li><a href="https://jona.im/">Jona Pomerance</a></li>
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
  margin: 20px 0 0;
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

.conclusion img {
  float: right;
  margin: 4px 0 0 31px;
  transition: opacity 0.2s ease-in-out;
}

.conclusion img:hover {
  opacity: 0.7;
}

.conclusion ul {
  margin: 3px 0 0;
  padding: 0;
  list-style: none;
}

.conclusion ul li {
  display: inline-block;
  margin-right: 6px;
}

.conclusion ul li:after {
  position: relative;
  content: '·';
  top: -1px;
  margin-left: 6px;
}

.conclusion ul li:last-child:after {
  display: none;
}

.conclusion p a, .conclusion ul li a {
  text-decoration: none;
  border-bottom: 2px solid rgba(255,255,255,0.2);
  color: inherit;
  transition: border-bottom 0.2s ease-in-out;
}

.conclusion p a:hover, .conclusion ul li a:hover {
  border-bottom-color: rgba(255,255,255,0.65);
}
</style>