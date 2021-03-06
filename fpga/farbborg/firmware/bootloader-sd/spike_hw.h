#ifndef SPIKEHW_H
#define SPIKEHW_H

#define PROMSTART 0x00000000
#define RAMSTART  0x00000800
#define RAMSIZE   0x400
#define RAMEND    (RAMSTART + RAMSIZE)

#define RAM_START 0x40000000
#define RAM_SIZE  0x04000000

#define FCPU      50000000

#define UART_RXBUFSIZE 32

/****************************************************************************
 * Types
 */
typedef unsigned int  uint32_t;    // 32 Bit
typedef signed   int   int32_t;    // 32 Bit

typedef unsigned short int  uint16_t;    // 16 Bit
typedef signed   short int   int16_t;    // 16 Bit

typedef unsigned char  uint8_t;    // 8 Bit
typedef signed   char   int8_t;    // 8 Bit


/****************************************************************************
 * Interrupt handling
 */
typedef void(*isr_ptr_t)(void);

void     irq_enable();
void     irq_disable();
void     irq_set_mask(uint32_t mask);
uint32_t irq_get_mak();

void     isr_init();
void     isr_register(int irq, isr_ptr_t isr);
void     isr_unregister(int irq);

/****************************************************************************
 * General Stuff
 */
void     halt();
void     jump(uint32_t addr);


/****************************************************************************
 * Timer
 */
#define TIMER_EN     0x08    // Enable Timer
#define TIMER_AR     0x04    // Auto-Reload
#define TIMER_IRQEN  0x02    // IRQ Enable
#define TIMER_TRIG   0x01    // Triggered (reset when writing to TCR)

typedef struct {
	volatile uint32_t tcr0;
	volatile uint32_t compare0;
	volatile uint32_t counter0;
	volatile uint32_t tcr1;
	volatile uint32_t compare1;
	volatile uint32_t counter1;
} timer_t;

void msleep(uint32_t msec);
void nsleep(uint32_t nsec);

void tic_init();


/***************************************************************************
 * GPIO0
 */
typedef struct {
	volatile uint32_t in0;
	volatile uint32_t in1;
	volatile uint32_t in2;
	volatile uint32_t in3;
	volatile uint32_t in4;
	volatile uint32_t in5;
	volatile uint32_t in6;
	volatile uint32_t in7;
	
	volatile uint32_t out0;
	volatile uint32_t out1;
	volatile uint32_t out2;
	volatile uint32_t out3;
	volatile uint32_t out4;
	volatile uint32_t out5;
	volatile uint32_t out6;
	volatile uint32_t out7;
	
	volatile uint32_t oe0;
	volatile uint32_t oe1;
	volatile uint32_t oe2;
	volatile uint32_t oe3;
	volatile uint32_t oe4;
	volatile uint32_t oe5;
	volatile uint32_t oe6;
	volatile uint32_t oe7;
	
} gpio_t;

/***************************************************************************
 * UART0
 */
#define UART_DR   0x01                    // RX Data Ready
#define UART_ERR  0x02                    // RX Error
#define UART_BUSY 0x10                    // TX Busy

typedef struct {
   volatile uint32_t ucr;
   volatile uint32_t rxtx;
} uart_t;

void uart_init();
void uart_putchar(char c);
void uart_putstr(char *str);
char uart_getchar();
/***************************************************************************
 * SPI0
 */
typedef struct{
	volatile uint32_t data;
	volatile uint32_t status;
	volatile uint32_t cs;
	volatile uint32_t dummy;
	volatile uint32_t divisor;
} spi_t;


/***************************************************************************
 * Pointer to actual components
 */
extern timer_t  *timer0;
extern uart_t   *uart0; 
extern gpio_t   *gpio0; 
extern uint32_t *sram0; 
extern spi_t    *spi0;

#endif // SPIKEHW_H
