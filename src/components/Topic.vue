<template>
  <div class="topic">
    <p v-for="(statement, index) in topic.statements" :key="index" :class="statement.speaker"><!--
   --><strong v-if="statement.speaker !== 'quote'">{{ speakerTitles[language][statement.speaker] }} </strong><!--
   --><span v-html="formatStatementText(statement.text[language])" /><!--
 --></p>
  </div>
</template>

<script>
export default {
  name: 'Topic',

  props: {
    topic: {
      type: Object,
      default: () => {}
    },
    language: {
      type: String,
      default: () => 'en'
    }
  },

  data () {
    return {
      speakerTitles: {
        en: {
          interviewee: 'user',
          interviewer: 'interviewer'  
        },
        de: {
          interviewee: 'userin',
          interviewer: 'interviewer'
        }
      }
    }
  },
  
  methods: {
    formatStatementText(text) {
      return text.replace('/*', '<span>').replace('*/', '</span>').replace(/\*(.*)\*/gim, '<em>$1</em>');
    }
  }
}
</script>

<style>
.topic {
  position: relative;
  background: #f6f6f6;
  z-index: 1;
  box-shadow: 0 30px 0 0 #f6f6f6, 0 -30px 0 0 #f6f6f6;
}

.topic:before {
  top: 0;
}

.topic:after {
  bottom: 0;
}

.topic p {
  margin: 22px 0 0;
  -webkit-hyphens: auto;
  hyphens: auto;
}

.topic p strong {
  margin-right: 6px;
}

.topic p span span {
  background: #FFE600;
  box-shadow: -3px 0 0 0 #FFE600, 3px 0 0 0 #FFE600;
}

.topic p.quote {
  margin: 22px 0 23px;
  font-size: 34px;
  font-weight: bold;
  line-height: 1;
  text-transform: uppercase;
  letter-spacing: 0.02em;
  color: #fff;
  
  -webkit-hyphens: none;
  hyphens: none;
  -webkit-text-stroke: 1px #000;
  text-stroke: 1px #000;
}

.topic p.quote:before, .topic p.quote:after {
  position: relative;
}

#app.de .topic p.quote:before, #app.de .topic p.quote:after {
  top: -3px;
}

.topic p.quote:before {
  content: '“';
  margin-left: -19px;
  margin-right: 0.1px;
}

#app.de .topic p.quote:before {
  content: '»';
  margin-left: -21px;
  margin-right: 1px;
}

.topic p.quote:after {
  content: '”';
}

#app.de .topic p.quote:after {
  content: '«';
}
</style>