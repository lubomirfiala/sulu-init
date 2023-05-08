<template>
  <div
      ref="fullscreenGallery"
  >
    <div
        v-if="display"
        class="fullscreen-gallery"
        @keydown="key"
    >
      <div
          class="images"
          @click="close()"
      >
        <div
            class="slides"
            :style="{ width : slidesWidth , left : slidesLeft}"
        >
          <div
              v-for="(image, key) in images "
              :key="key"
              class="slide"
          >
            <img :src="image.src" :alt="image.alt">
          </div>
        </div>


      </div>
      <div
          class="gbtn close"
          :class="{ showButtons: showButtonsState }"
          @click="close()"
      >
        <div class="line one"></div>
        <div class="line two"></div>
      </div>
      <div
          class="gbtn left"
          :class="{ showButtons: showButtonsState }"
          @click="prevSlide()"
      >
        <div class="line one"></div>
        <div class="line two"></div>
      </div>
      <div
          class="gbtn right"
          :class="{ showButtons: showButtonsState }"
          @click="nextSlide()"
      >
        <div class="line one"></div>
        <div class="line two"></div>
      </div>
    </div>
  </div>
</template>

<script>

const imageSources = document.querySelectorAll('[data-gallery]');

export default {
  name: 'fullscreenGallery',
  data() {
    return {
      display: false,
      images: [],
      activeSlide: 0,
      showButtonsState: false,
      showButtonsTimeout: null,
    };
  },
  computed: {
    slidesWidth() {
      return this.images.length * 100 + '%';
    },
    slidesLeft() {
      return '-' + this.activeSlide * 100 + '%';
    },
  },
  mounted() {
    let i = 0;
    imageSources.forEach((imageSource ) => {
      let image = {
        src: imageSource.getAttribute('data-gallery'),
        alt: imageSource.getAttribute('alt'),
      };
      imageSource.setAttribute('data-gallery-n', i)
      this.images.push(image);
      imageSource.addEventListener('click', () => {
        this.open(imageSource.getAttribute('data-gallery-n'));
      })
      i++;
    })

    this.$refs.fullscreenGallery.addEventListener('mousemove', (event) => {
      this.showButtons();
    });

    window.addEventListener("keydown", (e) => {
      if(e.key === 'ArrowRight') { this.nextSlide(); }
      if(e.key === 'ArrowLeft') { this.prevSlide(); }
      if(e.key === 'Escape') { this.close(); }
    });

    console.log(this.images);
  },
  methods: {
    open(i = 0) {
      console.log(i);
      this.display = true;
      this.activeSlide = parseInt(i);
    },
    close() {
      this.display = false;
    },
    nextSlide() {
      this.activeSlide++;
      if (this.activeSlide >= this.images.length ) {
        this.activeSlide = 0;
      }
    },
    prevSlide() {
      if(this.activeSlide === 0) {
        this.activeSlide = this.images.length;
      }
      this.activeSlide--;
    },
    showButtons() {
      this.showButtonsState=true;
      this.showButtonsTimeout = setTimeout(()=>{
        this.showButtonsState=false;
      }, 2000);
    },
    key() {
      console.log('key');
    }
  },
}
</script>

<style lang="scss">
.fullscreen-gallery {
  position: fixed;
  top:0;
  left: 0;
  right: 0;
  bottom: 0;
  background: #000000aa;
  -webkit-backdrop-filter: blur(.5rem) saturate(180%);
  backdrop-filter: blur(.5rem) saturate(180%);
  z-index: 662607015;
  display: grid;
  padding: 1rem;
  grid-template-columns: 4rem 1fr 4rem;
  grid-template-rows: 4rem 1fr 4rem;
  .gbtn {
    position: relative;
    margin: .5rem;
    line-height: 3rem;
    height: 3rem;
    color: #ffffff;
    border-radius: 3rem;
    background: #00000088;
    -webkit-backdrop-filter: blur(.5rem) saturate(180%);
    backdrop-filter: blur(.5rem) saturate(180%);
    width: 3rem;
    padding:0;
    text-align:center;
    z-index: 1;
    align-self:center;
    justify-self: center;
    cursor: pointer;
    opacity: 1;
    transition: opacity 1s;
    .line {
      height: 2px;
      background: #ffffff;
      position: absolute;
      width: 2rem;
      top: calc(1.5rem - 1px);
      left: .5rem;
      &.one {
        transform: rotate(45deg);
      }
      &.two {
        transform: rotate(-45deg);
      }
    }
    &:hover {
      background: #ffffff;
      color: #000000;
      opacity: 1;
      .line {
        background: #000000;
      }
    }
    &.close {
      grid-column: 3 / 4;
      grid-row: 1/2 ;
    }
    &.left {
      grid-column: 1 / 2;
      grid-row: 2 / 3 ;
      .line {
        width: 1rem;
        left: .9rem;
        &.two {
          top: 1.05rem;
        }
        &.one {
          top: 1.75rem;
        }
      }
    }
    &.right {
      grid-column: 3 / 4;
      grid-row: 2 / 3 ;
      .line {
        width: 1rem;
        left: 1.1rem;
        &.one {
          top: 1.05rem;
        }
        &.two {
          top: 1.75rem;
        }
      }
    }
  }
  .images {
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    position: absolute;
    color: #ffffff;
    overflow: hidden;
    .slides {
      top: 0;
      bottom: 0;
      position: absolute;
      display: flex;
      transition: all .3s;
      .slide {
        //border: 1px solid #ffffff;
        position:relative;
        flex-basis: 0;
        flex-grow: 1;
        padding: 1rem;
        display: flex;
        justify-items: center;
        align-items: center;
        img {
          width: auto;
          margin: auto;
          max-width: calc( 100% - 2rem );
          max-height: calc( 100% - 2rem );
          border-radius: .5rem;
        }
      }
    }
  }
  @media (min-width: 60rem) {
    //grid-template-columns: 4rem 1fr 4rem 15rem;
    .gbtn {
      opacity: 0;
      &.showButtons {
        opacity: 1;
        transition: opacity .3s;
      }
    }
  }
}

</style>
