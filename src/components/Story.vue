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
      <div :class="'visualization' + (currentlyWithinStory ? ' story' : '')">
        <div class="data">
          <div class="clock">{{ currentTimestampClock }}</div>
          <div class="device">
            <div ref="screen" :class="'screen ' + currentContentType"></div>
            <ExampleTouches />
            <div class="status content">
              <span v-if="language === 'de'">App-Ansicht</span>
              <span v-else>Viewing</span>
              {{ contentTypeTitles[language][currentContentType] }}
            </div>
            <div class="status interaction">
              <span v-if="language === 'de'">Letzte Interaktion</span>
              <span v-else>Last Interaction</span>
              {{ mostRecentTouchTitle }}
            </div>
          </div>
          <div class="legend">
            <div class="timestamp">Timestamp</div>
            <div class="navigation-1">Navigation</div>
            <div class="navigation-2">Navigation</div>
            <div class="touch-area">
              <span v-if="language === 'de'">Touch-<br>Fläche</span>
              <span v-else>Touch<br>Area</span>
            </div>
            <div class="tap">Touch</div>
            <div class="swipe">Swipe</div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import Topic from './Topic.vue';
import ExampleTouches from './ExampleTouches.vue';

const seedrandom = require('seedrandom');

export default {
  name: 'Story',

  components: {
    Topic,
    ExampleTouches
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
    recentTouches() {
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
    mostRecentTouch() {
      let reverseTouches = this.touches.slice().reverse();
      const mostRecentTouch = reverseTouches.find(touch => touch.timestamp <= this.currentTimestamp);

      if (mostRecentTouch) {
        return mostRecentTouch;
      } else {
        return this.touches[0];
      }
    },
    mostRecentTouchTitle() {
      const contextSensitiveAction = this.contextAwareTouchAction(this.mostRecentTouch.action);

      if (contextSensitiveAction in this.touchTitles[this.language][this.currentContentType]) {
        return this.touchTitles[this.language][this.currentContentType][contextSensitiveAction];
      } else {
        return this.mostRecentTouch.action; 
      }
    },
    currentContentType() {
      return this.mostRecentTouch.content;
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
        } else {
          closestTopicOffset = this.topics[closestTopicIndex].cumulativeHeight;
        }
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
    contextAwareTouchAction(action) {
      if (action in this.touchReplacements[this.currentContentType]) {
        return this.touchReplacements[this.currentContentType][action];
      } else {
        return action;
      }
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
    calculateCoordinate(coordinate, touchIndex, parameters, diameter) {
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
    recentTouches: function(updatedRecentTouches) {
      const screen = this.$refs.screen;
      screen.textContent = '';

      if (updatedRecentTouches.length) {
        updatedRecentTouches.forEach(function(touch) {
          const contextSensitiveAction = this.contextAwareTouchAction(touch.action);

          if (contextSensitiveAction in this.touchParameters) {
            const touchIndex = this.touchIndexesByTimestamp[touch.timestamp],
                  touchRangeProgress = (this.currentTimestamp - touch.timestamp) / this.millisecondsTouchRange,
                  parameters = this.touchParameters[contextSensitiveAction];
            
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
                  touchElement.style.left = (this.calculateCoordinate('x', touchIndex, parameters, this.touchSwipeStepDiameter) + i * parameters.xTranslate) + 'px';
                  touchElement.style.top = this.calculateCoordinate('y', touchIndex, parameters, this.touchSwipeStepDiameter) + 'px';
                } else {
                  touchElement.style.left = this.calculateCoordinate('x', touchIndex, parameters, this.touchSwipeStepDiameter) + 'px';
                  touchElement.style.top = (this.calculateCoordinate('y', touchIndex, parameters, this.touchSwipeStepDiameter) + i * parameters.yTranslate) + 'px';
                }

                let offsetTouchRangeProgress = 1 - touchRangeProgress + i * (this.swipeDurationFactor / (parameters[swipeAxis + 'Count'] - 1));
                if (offsetTouchRangeProgress > 1 || offsetTouchRangeProgress < 0) offsetTouchRangeProgress = 0;
                touchElement.style.transform = 'scale(' + this.easeIn(offsetTouchRangeProgress) + ')';

                screen.appendChild(touchElement);
              }
            } else if (('x' in parameters || 'xMin' in parameters && 'xMax' in parameters) && ('y' in parameters || 'yMin' in parameters && 'yMax' in parameters)) {
              const touchElement = document.createElement('div');
              
              touchElement.className = 'touch tap';
              touchElement.style.width = this.touchTapDiameter + 'px';
              touchElement.style.height = this.touchTapDiameter + 'px';
              touchElement.style.left = this.calculateCoordinate('x', touchIndex, parameters, this.touchTapDiameter) + 'px';
              touchElement.style.top = this.calculateCoordinate('y', touchIndex, parameters, this.touchTapDiameter) + 'px';
              touchElement.style.transform = 'scale(' + this.easeIn(1 - (touchRangeProgress > 1 ? 1 : touchRangeProgress)) + ')';

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
  transition: all 0.1s ease-in-out;
}

.visualization .data .device .screen.legend {
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
  opacity: 0;
  transition: opacity 0.2s ease-in-out;
}

.visualization.story .data .status {
  opacity: 1;
}

.visualization .data .status.content {
  bottom: 100%;
  margin-bottom: 19px;
}

.visualization .data .status.interaction {
  top: 100%;
  margin-top: 21px;
}

.visualization .data .status span {
  display: block;
  font-size: 12px;
  line-height: 1.4;
  text-transform: uppercase;
  letter-spacing: 0.025em;
  color: rgba(0,0,0,0.3);
}

.visualization .data .clock {
  position: absolute;
  top: 50%;
  right: 0;
  margin-top: -10px;
  padding-left: 6px;
  font-size: 16px;
  font-weight: bold;
  font-variant-numeric: slashed-zero;
  line-height: 20px;
}

.visualization .data .clock:before {
  position: absolute;
  display: block;
  content: '';
  top: 10px;
  right: 100%;
  width: 100px;
  height: 1px;
  background: #000;
  z-index: -1;
}

.visualization .legend {
  transition: opacity 0.2s ease-in-out !important;
}

.visualization.story .legend {
  opacity: 0;
}

.visualization .legend div {
  position: absolute;
  display: inline-block;
  font-size: 14px;
  font-weight: 400;
  font-style: italic;
  line-height: 20px;
  color: rgba(0,0,0,0.6);
}

.visualization .legend div:before {
  position: absolute;
  display: block;
  content: '';
  top: 10px;
  height: 1px;
  background: rgba(0,0,0,0.4);
}

.visualization .legend div.timestamp {
  top: 236px;
  right: 12px;
}

.visualization .legend div.navigation-1 {
  top: 107px;
  right: 89px;
  padding-left: 5px;
}

.visualization .legend div.navigation-2 {
  top: 417px;
  right: 89px;
  padding-left: 5px;
}

.visualization .legend div.navigation-1:before, .visualization .legend div.navigation-2:before {
  right: 100%;
  width: 46px;
}

.visualization .legend div.touch-area {
  top: 257px;
  right: 409px;
  padding-right: 6px;
  line-height: 14px;
}

.visualization .legend div.touch-area:before {
  top: 14px;
  left: 100%;
  width: 30px;
}

.visualization .legend div.tap {
  top: 189px;
  left: 129px;
}

.visualization .legend div.swipe {
  top: 321px;
  left: 189px;
}
</style>