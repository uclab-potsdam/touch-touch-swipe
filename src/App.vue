<template>
  <div id="app" :class="language">
    <Introduction
      ref="introduction"
      :introduction="introduction"
      :language="language"
    />
    <Story
      :topics="interview"
      :touches="touches"
      :language="language"
      :elementOffset="introductionHeight"
    />
    <Conclusion
      :conclusion="conclusion"
      :language="language"
    />
    <div class="languages">
      <a
        v-for="(linkLanguage, index) in supportedLanguages"
        :key="index"
        :class="linkLanguage + (linkLanguage === language ? ' active' : '')"
        href="#"
        @click="updateLanguage"
      >
        {{ linkLanguage }}
      </a>
    </div>
  </div>
</template>

<script>
import Introduction from './components/Introduction.vue';
import Story from './components/Story.vue';
import Conclusion from './components/Conclusion.vue';

import introduction from './data/introduction.json';
import interview from './data/interview.json';
import conclusion from './data/conclusion.json';
import touches from './data/touches.json';

export default {
  name: 'App',

  components: {
    Introduction,
    Story,
    Conclusion
  },

  data () {
    return {
      introduction,
      interview,
      conclusion,
      touches,

      supportedLanguages: ['en', 'de'],
      language: 'en',
      introductionHeight: 0
    }
  },

  methods: {
    updateLanguage(event) {
      if (event) {
        this.language = event.target.classList[0];
        event.preventDefault();
      }
    },
    updateIntroductionHeight() {
      this.introductionHeight = this.$refs.introduction.$el.offsetHeight;
      window.dispatchEvent(new CustomEvent('scroll'));
    }
  },

  watch: {
    language: function(newLanguage) {
      document.documentElement.setAttribute('lang', newLanguage);

      this.$nextTick(function() {
        this.updateIntroductionHeight();
      });
    }
  },

  created () {
    document.title = 'touch, touch, swipe';
    document.documentElement.setAttribute('lang', this.language);
  },

  mounted () {
    this.updateIntroductionHeight();
    document.addEventListener('readystatechange', this.updateIntroductionHeight);
  }
}
</script>

<style>
@import './assets/normalize.css';
@import './assets/fonts.css';

#app {
  position: relative;
  width: 100%;
  min-width: 1080px;
  height: 100%;
  font-family: 'PlexSansWeb', 'IBM Plex Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
  font-size: 16px;
  line-height: 1.25;
  background: #f6f6f6;
}

.wrapper {
  max-width: 960px;
  margin: 0 auto;
  padding: 0 60px;
}

.languages {
  position: fixed;
  top: 15px;
  right: 17px;
  z-index: 0;
}

.languages a {
  padding: 0 3px;
  font-weight: 700;
  text-transform: uppercase;
  text-decoration: none;
  color: rgba(9,9,9,0.3);
  transition: color 0.1s ease-in-out;
}

.languages a:hover, .languages a.active {
  color: #090909;
}
</style>