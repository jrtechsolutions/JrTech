import { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { 
  Server, Shield, Cloud, Headphones, 
  Network, Database,
  ArrowRight, CheckCircle2
} from 'lucide-react';
import { Button } from "@/components/ui/button";

const services = [
  {
    icon: Server,
    title: 'Infraestrutura de TI',
    shortDesc: 'Servidores e ambientes otimizados',
    description: 'Planejamento e implementação de soluções de infraestrutura para garantir desempenho, estabilidade e organização do seu ambiente tecnológico.',
    features: ['Servidores físicos e virtuais (existentes no cliente)', 'Configuração de datacenter local ou sala técnica', 'Backup e recuperação de dados', 'Organização e padronização do ambiente'],
    color: 'blue',
  },
  {
    icon: Shield,
    title: 'Segurança Digital',
    shortDesc: 'Proteção contra ameaças',
    description: 'Soluções práticas e acessíveis de cibersegurança para proteger dados, prevenir ataques e manter sua empresa segura.',
    features: ['Firewall e antivírus corporativo', 'Auditoria e gestão de vulnerabilidades', 'Monitoramento básico de segurança', 'Resposta a incidentes e remoção de ameaças'],
    color: 'green',
  },
  {
    icon: Cloud,
    title: 'Cloud Computing',
    shortDesc: 'Migração e gestão em nuvem',
    description: 'Levamos sua empresa para a nuvem com segurança e eficiência, usando as melhores plataformas do mercado.',
    features: ['Google Workspace, Microsoft 365 e servidores cloud', 'Migração planejada de arquivos e sistemas', 'Otimização de custos e recursos', 'Ambientes híbridos e backup em nuvem'],
    color: 'cyan',
  },
  {
    icon: Headphones,
    title: 'Suporte Técnico',
    shortDesc: 'Assistência especializada',
    description: 'Atendimento remoto e presencial para manter sua operação funcionando sem interrupções.',
    features: ['Help desk remoto', 'Suporte presencial sob demanda', 'SLA definido por contrato', 'Atendimento emergencial 24/7 para clientes do plano'],
    color: 'purple',
  },
  {
    icon: Network,
    title: 'Redes e Conectividade',
    shortDesc: 'Infraestrutura de rede',
    description: 'Implantação e organização de redes corporativas com alto desempenho e estabilidade.',
    features: ['Cabeamento estruturado', 'Otimização e expansão de Wi-Fi corporativo', 'VPN segura para equipes remotas', 'Segmentação e organização da rede'],
    color: 'orange',
  },
  {
    icon: Database,
    title: 'Gestão de Dados',
    shortDesc: 'Banco de dados e BI',
    description: 'Organização e administração de dados para melhorar a tomada de decisões e aumentar a eficiência.',
    features: ['Administração de bancos de dados existentes', 'Dashboards e relatórios inteligentes (Data Studio / Power BI)', 'Integração de sistemas e automação de dados', 'Performance e otimização de consultas'],
    color: 'pink',
  },
];

const colorVariants: Record<string, string> = {
  blue: 'from-blue-500 to-blue-600 group-hover:shadow-blue-500/30',
  green: 'from-emerald-500 to-emerald-600 group-hover:shadow-emerald-500/30',
  cyan: 'from-cyan-500 to-cyan-600 group-hover:shadow-cyan-500/30',
  purple: 'from-purple-500 to-purple-600 group-hover:shadow-purple-500/30',
  orange: 'from-orange-500 to-orange-600 group-hover:shadow-orange-500/30',
  pink: 'from-pink-500 to-pink-600 group-hover:shadow-pink-500/30',
};

export default function ServicesSection() {
  const [activeService, setActiveService] = useState<number | null>(null);

  const scrollToContact = () => {
    const element = document.querySelector('#contato');
    if (element) {
      element.scrollIntoView({ behavior: 'smooth' });
    }
  };

  return (
    <section id="servicos" className="relative py-24 bg-slate-900 overflow-hidden">
      {/* Background */}
      <div className="absolute inset-0">
        <div className="absolute inset-0 bg-[linear-gradient(rgba(59,130,246,0.02)_1px,transparent_1px),linear-gradient(90deg,rgba(59,130,246,0.02)_1px,transparent_1px)] bg-[size:50px_50px]" />
        <div className="absolute top-1/4 right-0 w-96 h-96 bg-blue-500/5 rounded-full blur-3xl" />
        <div className="absolute bottom-1/4 left-0 w-80 h-80 bg-cyan-500/5 rounded-full blur-3xl" />
      </div>

      <div className="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Header */}
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="text-center mb-16"
        >
          <span className="inline-block px-4 py-1.5 bg-blue-500/10 border border-blue-500/20 rounded-full text-blue-400 text-sm font-medium mb-4">
            Nossos Serviços
          </span>
          <h2 className="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-4">
            Soluções completas para sua{' '}
            <span className="bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">
              transformação digital
            </span>
          </h2>
          <p className="text-slate-400 text-lg max-w-2xl mx-auto">
            Do planejamento à execução, oferecemos um portfólio completo de serviços 
            para atender todas as necessidades tecnológicas da sua empresa.
          </p>
        </motion.div>

        {/* Services Grid */}
        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          {services.map((service, index) => (
            <motion.div
              key={service.title}
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.5, delay: index * 0.1 }}
              className="group relative"
              onMouseEnter={() => setActiveService(index)}
              onMouseLeave={() => setActiveService(null)}
            >
              <div className="relative h-full p-6 bg-slate-800/50 backdrop-blur-sm rounded-2xl border border-slate-700/50 hover:border-slate-600 transition-all duration-500 overflow-hidden">
                {/* Gradient Background on Hover */}
                <div className={`absolute inset-0 bg-gradient-to-br ${colorVariants[service.color].split(' ')[0]} ${colorVariants[service.color].split(' ')[1]} opacity-0 group-hover:opacity-5 transition-opacity duration-500`} />

                {/* Icon */}
                <motion.div 
                  className={`inline-flex p-3 rounded-xl bg-gradient-to-br ${colorVariants[service.color]} shadow-lg group-hover:shadow-xl transition-shadow duration-300 mb-5`}
                  whileHover={{ scale: 1.1, rotate: 5 }}
                  transition={{ type: "spring", stiffness: 400, damping: 10 }}
                >
                  <motion.div
                    animate={{ 
                      y: activeService === index ? [0, -3, 0] : 0,
                    }}
                    transition={{ 
                      duration: 1.5, 
                      repeat: activeService === index ? Infinity : 0,
                      ease: "easeInOut"
                    }}
                  >
                    <service.icon className="w-6 h-6 text-white" />
                  </motion.div>
                </motion.div>

                {/* Content */}
                <h3 className="text-xl font-semibold text-white mb-2 group-hover:text-blue-400 transition-colors">
                  {service.title}
                </h3>
                <p className="text-slate-500 text-sm mb-4">
                  {service.shortDesc}
                </p>
                <p className="text-slate-400 mb-5">
                  {service.description}
                </p>

                {/* Features List */}
                <AnimatePresence>
                  <motion.ul
                    initial={{ opacity: 0, height: 0 }}
                    animate={{ 
                      opacity: activeService === index ? 1 : 0.7,
                      height: 'auto'
                    }}
                    className="space-y-2 mb-5"
                  >
                    {service.features.map((feature) => (
                      <li key={feature} className="flex items-center gap-2 text-sm text-slate-400">
                        <CheckCircle2 className="w-4 h-4 text-blue-400 flex-shrink-0" />
                        {feature}
                      </li>
                    ))}
                  </motion.ul>
                </AnimatePresence>

              </div>
            </motion.div>
          ))}
        </div>

        {/* Bottom CTA */}
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6, delay: 0.4 }}
          className="mt-16 text-center"
        >
          <p className="text-slate-400 mb-6">
            Não encontrou o que procura? Temos soluções personalizadas para sua necessidade.
          </p>
          <Button
            onClick={scrollToContact}
            className="bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-cyan-500 text-white px-8 py-4 rounded-2xl font-semibold shadow-xl shadow-blue-500/25 hover:shadow-blue-500/40 transition-all duration-300"
          >
            Fale com um Especialista
            <ArrowRight className="w-5 h-5 ml-2" />
          </Button>
        </motion.div>
      </div>
    </section>
  );
}
