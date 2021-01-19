<template>
  <div></div>
</template>

<script>
/* eslint-disable */
import axios from 'axios'
// import { mapState } from 'vuex'

export default {
  data() {
    return {
      id: 0,
      eventType: {
        mouseMove: 10,
        windowResize: 20,
        stepChange: 30,
        stepProgressChange: 40
      },
      packageType: {
        init: 10,
        stack: 20
      },
      packageCounter: 0,
      eventStack: [],
      hostUrl: 'uclab.fh-potsdam.de',
      instrumentationUrl: '/instrumentation.php'
    }
  },
  // computed: {
  //   ...mapState({
  //     scrollStep: (state) =>
  //       state.scrollytelling ? state.scrollytelling.step : null,
  //     scrollProgress: (state) =>
  //       state.scrollytelling ? state.scrollytelling.progress : null
  //   })
  // },
  // watch: {
  //   scrollStep(newValue, oldValue) {
  //     this.handleStepChange(newValue)
  //   },
  //   scrollProgress(newValue, oldValue) {
  //     this.handleProgressChange(newValue)
  //   }
  // },
  mounted() {
    if (window.location.href.includes(this.hostUrl)) {
      document.onmousemove = this.handleMouseMove
      window.onresize = this.handleWindowResize
      this.initLogging()
    }
  },
  methods: {
    // log handling
    initLogging() {
      this.id = Date.now()
      console.log(this.id)
      this.sendInitialLog()
      window.setInterval(this.sendLog, 5000)
    },
    logEvent(type, data) {
      const eventData = {
        timeOffset: Date.now() - this.id,
        type,
        data
      }
      this.eventStack.push(eventData)
    },

    // handle events
    handleMouseMove(event) {
      this.logEvent(this.eventType.mouseMove, this.getMousePosition())
    },
    handleWindowResize() {
      this.logEvent(this.eventType.windowResize, this.getWindowSize())
    },
    handleStepChange(newStep) {
      this.logEvent(this.eventType.stepChange, newStep)
    },
    handleProgressChange(newProgress) {
      this.logEvent(this.eventType.stepProgressChange, newProgress)
    },

    // server communication
    sendInitialLog() {
      const data = {
        windowSize: this.getWindowSize()
      }
      this.sendPackage(this.packageType.init, data)
    },
    sendLog() {
      if (this.eventStack.length > 0) {
        const data = this.eventStack
        this.eventStack = []
        this.sendPackage(this.packageType.stack, data)
      }
    },
    sendPackage(type, data) {
      axios
        .post(this.instrumentationUrl, {
          id: this.id,
          num: this.packageCounter,
          type,
          data
        })
        .then(function(response) { console.log(response) })
        .catch(function(error) {
          console.log(error)
        })

      this.packageCounter++
    },

    // helper functions
    getWindowSize() {
      return {
        x: window.innerWidth,
        y: window.innerHeight
      }
    },
    getMousePosition(event) {
      let eventDoc, doc, body

      event = event || window.event // IE-ism

      // If pageX/Y aren't available and clientX/Y are,
      // calculate pageX/Y - logic taken from jQuery.
      // (This is to support old IE)
      if (event.pageX == null && event.clientX != null) {
        eventDoc = (event.target && event.target.ownerDocument) || document
        doc = eventDoc.documentElement
        body = eventDoc.body

        event.pageX =
          event.clientX +
          ((doc && doc.scrollLeft) || (body && body.scrollLeft) || 0) -
          ((doc && doc.clientLeft) || (body && body.clientLeft) || 0)
        event.pageY =
          event.clientY +
          ((doc && doc.scrollTop) || (body && body.scrollTop) || 0) -
          ((doc && doc.clientTop) || (body && body.clientTop) || 0)
      }

      // Use event.pageX / event.pageY here
      return {
        x: event.pageX,
        y: event.pageY
      }
    }
  }
}
</script>
