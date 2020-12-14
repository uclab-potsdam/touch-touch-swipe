<template>
  <section class="story">
    <div class="wrapper">
      <div class="interview">
        <Topic
          v-for="(topic, index) in topics"
          :key="index"
          ref="topics"
          :topic="topic"
          :language="language"
          :style="topicMargins(index)"
        />
      </div>
      <div :class="'visualization' + (currentlyWithinStory ? ' story' : '') + (currentlyPaused ? ' paused' : '')">
        <div class="data">
          <div class="device">
            <div ref="screen" :class="'screen ' + currentContentType"></div>
            <ExampleTouches />
            <div class="status content">
              <span v-if="language === 'de'">App-Ansicht</span>
              <span v-else>Viewing</span>
              {{ contentTypeTitles[language][currentContentType] }}
            </div>
            <div class="status clock">
              <span v-if="language === 'de'">{{ currentlyPaused ? 'Pausiert' : 'Uhrzeit' }}</span>
              <span v-else>{{ currentlyPaused ? 'Paused' : 'Clock' }}</span>
              {{ currentTimestampClock }}
            </div>
          </div>
          <div class="interactions">
            <div class="previous-3">{{ touchTitleForIndex(mostRecentTouchIndex - 3) }}</div>
            <div class="previous-2">{{ touchTitleForIndex(mostRecentTouchIndex - 2) }}</div>
            <div class="previous-1">{{ touchTitleForIndex(mostRecentTouchIndex - 1) }}</div>
            <div class="previous-0">{{ touchTitleForIndex(mostRecentTouchIndex) }}</div>
          </div>
          <div class="legend">
            <ExampleInteractions :language="language" />
            <div class="labels">
              <div class="last-interactions">
                <span v-if="language === 'de'">Letzte Interaktionen</span>
                <span v-else>Last Interactions</span>
              </div>
              <div class="touch-area">
                <span v-if="language === 'de'">Touch-<br>Fläche</span>
                <span v-else>Touch<br>Area</span>
              </div>
              <div class="touch">Touch</div>
              <div class="swipe">Swipe</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import Topic from './Topic.vue';
import ExampleTouches from './ExampleTouches.vue';
import ExampleInteractions from './ExampleInteractions.vue';

const seedrandom = require('seedrandom');

export default {
  name: 'Story',

  components: {
    Topic,
    ExampleTouches,
    ExampleInteractions
  },

  props: {
    topics: {
      type: Array,
      default: () => []
    },
    touches: {
      type: Array,
      default: () => []
    },
    language: {
      type: String,
      default: () => 'en'
    },
    elementOffset: {
      type: Number,
      default: () => 0
    }
  },

  data () {
    return {
      windowHeight: 0,
      visualizationPixelsAboveCenter: 0, // currently not working correctly

      millisecondsPaddingTop: 20000,
      millisecondsPaddingBottom: 45000,
      millisecondsPerPixel: 90,
      millisecondsTouchRange: 7500,
      swipeDurationFactor: 0.25,

      currentTimestamp: 0,
      currentlyWithinStory: false,
      currentlyPaused: false,

      contentTypeTitles: {
        en: {
          feed: 'Feed',
          stories: 'Stories',
          igtv: 'IGTV Video'
        },
        de: {
          feed: 'Feed',
          stories: 'Stories',
          igtv: 'IGTV-Video'
        }
      },

      touchTapDiameter: 24,
      touchSwipeStepDiameter: 14,
      touchParameters: {
        tap_generic: {
          xMin: 16,
          xMax: 174,
          yMin: 130,
          yMax: 300
        },
        tap_left: {
          xMin: 16,
          xMax: 54,
          yMin: 112,
          yMax: 227
        },
        tap_right: {
          xMin: 136,
          xMax: 176,
          yMin: 112,
          yMax: 227
        },
        tap_back: {
          x: 6,
          y: 6
        },
        tap_like: {
          x: 6,
          yMin: 200,
          yMax: 282
        },
        tap_navigation: {
          xMin: 6,
          xMax: 184,
          y: 312
        },
        swipe_up: {
          xMin: 74,
          xMax: 176,
          yCount: 12,
          yTranslate: -9,
          yStartMin: 259,
          yStartMax: 304
        },
        swipe_down: {
          xMin: 74,
          xMax: 176,
          yCount: 12,
          yTranslate: 9,
          yStartMin: 151,
          yStartMax: 203
        },
        swipe_left: {
          xCount: 12,
          xTranslate: -9,
          xStartMin: 151,
          xStartMax: 124,
          yMin: 75,
          yMax: 195
        },
        swipe_right: {
          xCount: 12,
          xTranslate: 9,
          xStartMin: 25,
          xStartMax: 52,
          yMin: 75,
          yMax: 195
        }
      },
      touchReplacements: {
        feed: {
          tap_left: 'tap_generic',
          tap_right: 'tap_generic',
          'tap_left tap_like': 'tap_like'
        },
        stories: {
          tap_back: 'tap_left',
          tap_navigation: 'tap_right',
          'tap_left tap_like': 'tap_left',
        },
        igtv: {
          tap_left: 'tap_generic',
          tap_right: 'tap_generic'
        }
      },
      touchTitles: {
        en: {
          feed: {
            tap_generic: 'Tap on Post',
            tap_back: 'Back to Main Feed',
            tap_navigation: 'Navigation',
            tap_like: 'Like Post',
            swipe_up: 'Next Post',
            swipe_down: 'Previous Post',
            swipe_left: 'Next Carousel Image',
            swipe_right: 'Previous Carousel Image'
          },
          stories: {
            tap_left: 'Previous Story',
            tap_right: 'Next Story',
            swipe_up: 'More Details',
            swipe_down: 'More Details',
            swipe_left: 'Next Profile',
            swipe_right: 'Previous Profile'
          },
          igtv: {
            tap_generic: 'Tap on Post'
          }
        },
        de: {
          feed: {
            tap_generic: 'Touch auf Post',
            tap_back: 'Zurück zum Haupt-Feed',
            tap_navigation: 'Navigation',
            tap_like: 'Liken des Posts',
            swipe_up: 'Nächster Post',
            swipe_down: 'Vorheriger Post',
            swipe_left: 'Nächstes Karussell-Bild',
            swipe_right: 'Vorheriges Karussell-Bild'
          },
          stories: {
            tap_left: 'Vorherige Story',
            tap_right: 'Nächste Story',
            swipe_up: 'Mehr Details',
            swipe_down: 'Mehr Details',
            swipe_left: 'Nächstes Profil',
            swipe_right: 'Vorheriges Profil'
          },
          igtv: {
            tap_generic: 'Touch auf Post'
          }
        }
      }
    }
  },

  computed: {
    startTimestamp() {
      return this.touches[0].timestamp - this.millisecondsPaddingTop;
    },
    endTimestamp() {
      return this.touches[this.touches.length - 1].timestamp + this.millisecondsPaddingBottom;
    },
    currentTimestampClock() {
      const date = new Date(this.currentTimestamp);
      return this.formatDate(date);
    },
    touchIndexesByTimestamp() {
      const touchIndexes = [];
      this.touches.forEach(function(touch, index) {
        touchIndexes[touch.timestamp] = index;
      });
      return touchIndexes;
    },
    recentVisibleTouches() {
      if (this.currentlyWithinStory === true) {
        const pastTouchesInRange = this.touches.filter(
          touch => touch.timestamp <= this.currentTimestamp
                && touch.timestamp > this.currentTimestamp - this.millisecondsTouchRange * (1 + this.swipeDurationFactor)
        );
        return pastTouchesInRange;
      } else {
        return false;
      }
    },
    mostRecentTouchIndex() {
      const reverseTouches = this.touches.slice().reverse(),
            reverseMostRecentIndex = reverseTouches.findIndex(touch => touch.timestamp <= this.currentTimestamp);

      if (reverseMostRecentIndex !== -1) {
        return this.touches.length - (reverseMostRecentIndex + 1);
      } else {
        return false;
      }
    },
    currentContentType() {
      if (this.mostRecentTouchIndex === false) {
        return this.touches[0].content;
      } else {
        return this.touches[this.mostRecentTouchIndex].content;
      }
    }
  },

  methods: {
    updateCurrentTimestamp() {
      const windowScrollOffset = window.scrollY,
            currentTimestampOffset = windowScrollOffset + this.windowHeight / 2 + this.visualizationPixelsAboveCenter,
            timelineStartOffset = this.elementOffset - this.windowHeight / 2 + this.visualizationPixelsAboveCenter;

      if (timelineStartOffset > windowScrollOffset) {
        this.currentlyWithinStory = false;
      } else {
        this.currentlyWithinStory = true;
      }

      const topicOffsets = [];
      this.$refs.topics.forEach(function(topic) {
        topicOffsets.push(topic.$el.getBoundingClientRect().top + windowScrollOffset + this.visualizationPixelsAboveCenter * 2);
      }, this);
      const reverseTopicOffsets = topicOffsets.slice().reverse();

      const reverseClosestTopicIndex = reverseTopicOffsets.findIndex(topicOffset => topicOffset < currentTimestampOffset),
            closestTopicIndex = reverseTopicOffsets.length - (reverseClosestTopicIndex + 1);

      const elapsedTime = (windowScrollOffset - timelineStartOffset) * this.millisecondsPerPixel;
      let closestTopicOffset = 0;

      if (closestTopicIndex < reverseTopicOffsets.length) {
        if (currentTimestampOffset - reverseTopicOffsets[reverseClosestTopicIndex] <= this.topics[closestTopicIndex].elementHeight) {
          closestTopicOffset = this.topics[closestTopicIndex].cumulativeHeight
                               - this.topics[closestTopicIndex].elementHeight
                               + currentTimestampOffset
                               - reverseTopicOffsets[reverseClosestTopicIndex];
          this.currentlyPaused = true;
        } else {
          closestTopicOffset = this.topics[closestTopicIndex].cumulativeHeight;
          this.currentlyPaused = false;
        }
      } else {
        this.currentlyPaused = false;
      }

      this.currentTimestamp = this.startTimestamp + elapsedTime - closestTopicOffset * this.millisecondsPerPixel;
    },
    updateTopicHeights() {
      this.windowHeight = window.innerHeight;
      let cumulativeHeight = 0;

      this.topics.forEach(function(topic, index) {
        const elementHeight = this.$refs.topics[index].$el.offsetHeight;
        cumulativeHeight += elementHeight;

        topic.elementHeight = elementHeight;
        topic.cumulativeHeight = cumulativeHeight;
      }, this);

      this.updateCurrentTimestamp();
    },
    topicMargins(index) {
      let elapsedTime;

      if (index === 0) {
        elapsedTime = this.topics[index]['timestamp'] - this.startTimestamp + this.visualizationPixelsAboveCenter * 2 * this.millisecondsPerPixel;
      } else {
        elapsedTime = this.topics[index]['timestamp'] - this.topics[index - 1]['timestamp'];
      }

      let style = 'margin-top: ' + elapsedTime / this.millisecondsPerPixel + 'px';

      if (index === this.topics.length - 1) {
        const remainingTime = this.endTimestamp - this.topics[index]['timestamp'];
        style += '; margin-bottom: ' + remainingTime / this.millisecondsPerPixel + 'px';
      }

      return style;
    },
    contextAwareTouchAction(touch) {
      if (touch.action in this.touchReplacements[touch.content]) {
        return this.touchReplacements[touch.content][touch.action];
      } else {
        return touch.action;
      }
    },
    touchTitleForIndex(index) {
      if (index !== false && index >= 0 && index <= this.touches.length - 1) {
        const touch = this.touches[index];
        const contextAwareAction = this.contextAwareTouchAction(touch);

        if (contextAwareAction in this.touchTitles[this.language][touch.content]) {
          return this.touchTitles[this.language][touch.content][contextAwareAction];
        } else {
          return touch.action; 
        }
      } else {
        return '';
      }
    },
    touchCoordinate(coordinate, touchIndex, parameters, diameter) {
      if (coordinate in parameters) {
        return parameters[coordinate];
      } else {
        let variance;

        if (coordinate === 'x') {
          variance = seedrandom(touchIndex);
        } else {
          variance = seedrandom(this.touches.length + touchIndex);
        }

        if (coordinate + 'StartMin' in parameters) {
          return parameters[coordinate + 'StartMin'] + (parameters[coordinate + 'StartMax'] - parameters[coordinate + 'StartMin']) * variance();
        } else {
          return parameters[coordinate + 'Min'] + (parameters[coordinate + 'Max'] - diameter - parameters[coordinate + 'Min']) * variance();
        }
      }
    },
    touchColor(index, count) {
      if (index === count - 1) return '000000';
      else if (index === count - 2) return 'A39300';
      else if (index === count - 3) return 'C2AE00';
      else return 'E0CA00';
    },
    formatDate(date) {
        return date.getHours().toString().padStart(2, '0') + ':'
               + date.getMinutes().toString().padStart(2, '0') + ':'
               + date.getSeconds().toString().padStart(2, '0') + '.'
               + Math.floor(date.getMilliseconds() / 10).toString().padStart(2, '0');
    },
    easeIn(progress) {
      return progress * progress;
    }
  },

  watch: {
    recentVisibleTouches: function(updatedTouches) {
      const screen = this.$refs.screen;
      screen.textContent = '';

      const updatedTouchesCount = updatedTouches.length;

      if (updatedTouchesCount) {
        updatedTouches.forEach(function(touch, recentsIndex) {
          const contextAwareAction = this.contextAwareTouchAction(touch);

          if (contextAwareAction in this.touchParameters) {
            const touchIndex = this.touchIndexesByTimestamp[touch.timestamp],
                  touchRangeProgress = (this.currentTimestamp - touch.timestamp) / this.millisecondsTouchRange,
                  parameters = this.touchParameters[contextAwareAction];
            
            if (('xCount' in parameters && 'xTranslate' in parameters) || ('yCount' in parameters && 'yTranslate' in parameters)) {
              let swipeAxis;

              if ('x' in parameters || 'xStartMin' in parameters) {
                swipeAxis = 'x';
              } else {
                swipeAxis = 'y';
              }

              for (let i = 0; i < parameters[swipeAxis + 'Count']; i++) {
                const touchElement = document.createElement('div');
                
                touchElement.className = 'touch swipe-step';
                touchElement.style.width = this.touchSwipeStepDiameter + 'px';
                touchElement.style.height = this.touchSwipeStepDiameter + 'px';

                if (swipeAxis === 'x') {
                  touchElement.style.left = (this.touchCoordinate('x', touchIndex, parameters, this.touchSwipeStepDiameter) + i * parameters.xTranslate) + 'px';
                  touchElement.style.top = this.touchCoordinate('y', touchIndex, parameters, this.touchSwipeStepDiameter) + 'px';
                } else {
                  touchElement.style.left = this.touchCoordinate('x', touchIndex, parameters, this.touchSwipeStepDiameter) + 'px';
                  touchElement.style.top = (this.touchCoordinate('y', touchIndex, parameters, this.touchSwipeStepDiameter) + i * parameters.yTranslate) + 'px';
                }

                let offsetTouchRangeProgress = 1 - touchRangeProgress + i * (this.swipeDurationFactor / (parameters[swipeAxis + 'Count'] - 1));
                if (offsetTouchRangeProgress > 1 || offsetTouchRangeProgress < 0) offsetTouchRangeProgress = 0;
                touchElement.style.transform = 'scale(' + this.easeIn(offsetTouchRangeProgress) + ')';

                touchElement.style.backgroundColor = '#' + this.touchColor(recentsIndex, updatedTouchesCount);

                screen.appendChild(touchElement);
              }
            } else if (('x' in parameters || 'xMin' in parameters && 'xMax' in parameters) && ('y' in parameters || 'yMin' in parameters && 'yMax' in parameters)) {
              const touchElement = document.createElement('div');
              
              touchElement.className = 'touch tap';
              touchElement.style.width = this.touchTapDiameter + 'px';
              touchElement.style.height = this.touchTapDiameter + 'px';
              touchElement.style.left = this.touchCoordinate('x', touchIndex, parameters, this.touchTapDiameter) + 'px';
              touchElement.style.top = this.touchCoordinate('y', touchIndex, parameters, this.touchTapDiameter) + 'px';
              touchElement.style.transform = 'scale(' + this.easeIn(1 - (touchRangeProgress > 1 ? 1 : touchRangeProgress)) + ')';
              touchElement.style.backgroundColor = '#' + this.touchColor(recentsIndex, updatedTouchesCount);

              screen.appendChild(touchElement);
            }
          }
        }, this);
      }
    },
    language: function() {
      this.$nextTick(function() {
        this.updateTopicHeights();
      });
    }
  },

  mounted () {
    this.updateTopicHeights();
    window.addEventListener('scroll', this.updateCurrentTimestamp, false);
    window.addEventListener('resize', this.updateTopicHeights, false);
    window.addEventListener('orientationchange', this.updateTopicHeights, false);
  },

  beforeDestroy () {
    window.removeEventListener('scroll', this.updateCurrentTimestamp, false);
    window.removeEventListener('resize', this.updateTopicHeights, false);
    window.removeEventListener('orientationchange', this.updateTopicHeights, false);
  }
}
</script>

<style>
.interview {
  position: relative;
  display: inline-block;
  width: 440px;
  box-sizing: border-box;
}

.interview:after {
  position: absolute;
  display: block;
  content: '';
  top: 0;
  bottom: 0;
  left: 50%;
  width: 2px;
  margin-left: -1px;
  background: url('../assets/dot.svg') center fixed;
  opacity: 0.25;
}

@media (max-width: 1079px) {
  .interview:after {
    background-attachment: scroll;
  }
}

.visualization {
  position: fixed;
  top: 0;
  bottom: 0;
  left: 50%;
  width: 480px;
  opacity: 1;
}

.visualization * {
  box-sizing: border-box;
}

.visualization .data {
  position: relative;
  height: 540px;
  top: 50%;
  margin-top: -270px;
  text-align: center;
}

.visualization .data .device {
  position: relative;
  width: 210px;
  height: 430px;
  top: 50%;
  margin-left: 90px;
  border-radius: 28px;
  border: 1px solid #000;
  background: #fff;
  transform: translateY(-215px);
}

.visualization .data .device .screen {
  position: relative;
  display: inline-block;
  width: 190px;
  height: 340px;
  margin-top: 44px;
  border: 1px solid #000;
  background: url('../assets/screen-feed.svg') center no-repeat #FFE600;
}

.visualization .data .device .screen.legend {
  /*display: none;*/
  position: absolute;
  left: 9px;
}

.visualization .data .device .screen.stories {
  background-image: url('../assets/screen-stories.svg');
}

.visualization .data .device .screen.igtv {
  background-image: url('../assets/screen-igtv.svg');
}

.visualization .data .device .screen .touch {
  position: absolute;
  border-radius: 50%;
  background: #000;
  box-shadow: 0 0 0 1.5px #FFE600;
}

.visualization .data .status {
  position: absolute;
  left: -40px;
  right: -40px;
  font-weight: bold;
}

.visualization .data .status.content {
  bottom: 100%;
  margin-bottom: 19px;
}

.visualization .data .status.clock {
  top: 100%;
  margin-top: 21px;
  font-variant-numeric: slashed-zero;
}

.visualization .data .status span {
  display: block;
  font-size: 12px;
  line-height: 1.4;
  text-transform: uppercase;
  letter-spacing: 0.025em;
  color: rgba(0,0,0,0.3);
}

.visualization .data .interactions {
  position: absolute;
  top: 0;
  bottom: 0;
  left: 320px;
  right: -80px;
  text-align: left;
  font-size: 18px;
  font-weight: bold;
}

.visualization .data .interactions div {
  position: absolute;
  padding: 0 4px;
}

.visualization .data .interactions .previous-3 {
  top: 159px;
  color: rgba(0,0,0,0.07);
}

.visualization .data .interactions .previous-2 {
  top: 192px;
  color: rgba(0,0,0,0.12);
}

.visualization .data .interactions .previous-1 {
  top: 225px;
  color: rgba(0,0,0,0.23);
}

.visualization .data .interactions .previous-0 {
  top: 258px;
}

.visualization.paused .data .interactions .previous-0 {
  background: #FFE600;
}

.visualization .legend {
  /*display: none;*/
  transition: opacity 0.2s ease-in-out !important;
}

.visualization.story .legend {
  opacity: 0;
}

.visualization .legend .labels div {
  position: absolute;
  display: inline-block;
  font-size: 14px;
  font-weight: 400;
  font-style: italic;
  line-height: 20px;
  color: rgba(0,0,0,0.55);
}

.visualization .legend .labels div:before, .visualization .legend .labels div:after {
  position: absolute;
  display: block;
  content: '';
  background: rgba(0,0,0,0.375);
}

.visualization .legend .labels div.last-interactions {
  top: 290px;
  right: 14px;
  padding-top: 2px;
}

#app.de .visualization .legend .labels div.last-interactions {
  right: -7px;
}

.visualization .legend .labels div.last-interactions:before, .visualization .legend .labels div.last-interactions:after {
  top: 12px;
  width: 33px;
  height: 1px;
}

#app.de .visualization .legend .labels div.last-interactions:before, #app.de .visualization .legend .labels div.last-interactions:after {
  width: 35px;
}

.visualization .legend .labels div.last-interactions:before {
  left: 100%;
  margin-left: 5px;
}

.visualization .legend .labels div.last-interactions:after {
  right: 100%;
  margin-right: 5px;
}

.visualization .legend .labels div.navigation-1:before, .visualization .legend .labels div.navigation-2:before {
  right: 100%;
  width: 46px;
}

.visualization .legend .labels div.touch-area {
  top: 257px;
  right: 400px;
  padding-right: 5px;
  line-height: 14px;
}

.visualization .legend .labels div.touch-area:before {
  top: 14px;
  left: 100%;
  width: 21px;
  height: 1px;
}

.visualization .legend .labels div.touch {
  top: 189px;
  left: 129px;
}

.visualization .legend .labels div.swipe {
  top: 321px;
  left: 189px;
}
</style>