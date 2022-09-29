</div>
                                      </td>
                                    </tr>
                                  </tbody>
                                </table>

                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    
                    <!-- Links de interés -->
                    <?=$this->element('emails/public/links_interes', array(
                      'link' => array(
                        'url' => $footer['tienda_url'],
                        'texto' => 'Ir a la tienda'
                      )
                    )); ?>
                    <!-- /Links de interés -->

                    <!-- Información de la tienda -->
                    <?=$this->element('emails/public/tienda_info', array(
                      'tienda' => array(
                        'direccion' => $footer['bodega_direccion'],
                        'fono' => $footer['bodega_fono'],
                        'email' => 'ventas@toolmania.cl',
                        'horario_atencion' => 'Lun-Vie 09:00-14:00 y 15:00-18:30',
                        'mapa' => 'https://www.google.cl/maps/@-33.4481791,-70.8488174,17z'
                      )
                    )); ?>
                    <!-- /Información de la tienda -->

                    <!-- RRSS -->
                    <?= $this->element('emails/public/rrss'); ?>
                    <!-- /RRSS -->
                  

                    <!-- RRSS -->
                    <?= $this->element('emails/public/no_responder'); ?>
                    <!-- /RRSS -->

                  </td>
                </tr>
              </tbody>
            </table>
            <!--[if (gte mso 9)|(IE)]>
                </td>
              </tr>
            </tbody>
          </table>
        <![endif]-->
          </td>
        </tr>
      </tbody>
    </table>

  </div>
</body>

</html>