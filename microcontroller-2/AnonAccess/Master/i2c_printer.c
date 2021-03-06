/* i2c_printer.c */
/*
 *   This file is part of AnonAccess, an access system which can be used
 *    to open door or doing other things with an anonymity featured
 *    account managment.
 *   Copyright (C) 2006, 2007, 2008  Daniel Otte (daniel.otte@rub.de)
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


/**
 * 
 * 
 * 
 * 
 * 
 * 
 */

#include "config.h"
#include <stdint.h>
#include <avr/pgmspace.h>
#include <util/delay.h>
#include "mcp23017.h"
#include "i2c.h"
#include "i2c_tools.h"

#define ADDR (MCP23017_BASEADDR + PRINTER_ADDR)

#define STB   0
#define ACK   1
#define BUSY  2
#define PE    3
#define H_L   4
#define FAULT 5
#define RESET 6
  

uint8_t printer_available=0;

void printer_char(char c){
//	while(i2c_get_bit_in_8bit_reg(ADDR, GPIOB_B0, BUSY))
//		;
	if(!printer_available)
		return;
	_delay_ms(10);
	i2c_set_8bit_reg(ADDR, GPIOA_B0, c);
	_delay_ms(1);
	i2c_set_bit_in_8bit_reg(ADDR, GPIOB_B0, STB, 0);
	_delay_ms(1);
	i2c_set_bit_in_8bit_reg(ADDR, GPIOB_B0, STB, 1);
}

void printer_str(const char *str){
	while(*str){
		printer_char(*str++);
	}
}

void printer_hexdump(const void* buffer, uint16_t length){
	char tab[]="0123456789ABCDEF";
	while(length--){
		printer_char(tab[(*((uint8_t*)buffer))>>4]);
		printer_char(tab[(*((uint8_t*)buffer))&0x0f]);
		buffer = (uint8_t*)buffer +1;
	}
}

void printer_str_P(PGM_P str){
	char c;
	while((c=pgm_read_byte(str++))){
		printer_char(c);
	}
}

void printer_reset(void){
	if(!printer_available)
		return;
	i2c_set_bit_in_8bit_reg(ADDR, GPIOB_B0, RESET, 0);
	_delay_ms(3);
	i2c_set_bit_in_8bit_reg(ADDR, GPIOB_B0, RESET, 1);
}

void printer_init(void){
	if(!printer_available)
		return;
	i2c_set_8bit_reg(ADDR, IODIRA_B0, 0x00); /* set port a as output */
	i2c_set_8bit_reg(ADDR, IODIRB_B0, 0xBE); /* set port a as output */
	i2c_set_8bit_reg(ADDR, IOCON_B0, 0x20); /* set port a as output */
	i2c_set_8bit_reg(ADDR, GPIOB_B0, 0x41); /* set RESET & STB high */
}


